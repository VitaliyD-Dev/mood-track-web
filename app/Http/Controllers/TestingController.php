<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;

class TestingController extends Controller
{
    public function index()
    {
        $metrics = $this->calculateMetrics();
        return view('testing', compact('metrics'));
    }

    private function calculateMetrics()
    {
        try {
            $filePath = base_path('test_data.xlsx');

            if (!file_exists($filePath)) {
                Log::error('File not found: ' . $filePath);
                return ['error' => 'Файл test_data.xlsx не знайдено в кореневій директорії'];
            }

            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $data = $worksheet->toArray();

            Log::info('Excel file loaded', [
                'total_rows' => count($data),
                'headers' => $data[0] ?? []
            ]);

            if (count($data) < 2) {
                return ['error' => 'Файл не містить даних'];
            }

            // Пропускаємо заголовки
            $headers = array_shift($data);
            Log::info('Headers found', ['headers' => $headers]);

            $total = 0;
            $correct = 0;
            $emotions = ['joy', 'sadness', 'anger', 'disgust', 'surprise', 'neutral', 'fear'];
            $confusionMatrix = array_fill_keys($emotions, array_fill_keys($emotions, 0));
            $errorAnalysis = array_fill_keys($emotions, [
                'misclassifications' => array_fill_keys($emotions, 0),
                'examples' => []
            ]);

            // Ініціалізація змінних для уникнення помилок Undefined array key
            $classCounts = array_fill_keys($emotions, 0);
            $metricsData = [];
            $macroF1Sum = 0;
            $weightedF1Sum = 0;
            $totalSamples = 0;

            $invalidRows = [];
            foreach ($data as $rowIndex => $row) {
                Log::info('Processing row', [
                    'row_index' => $rowIndex + 2, // +2 because we removed headers and Excel is 1-based
                    'row_data' => $row
                ]);

                if (count($row) < 6) {
                    $invalidRows[] = [
                        'row' => $rowIndex + 2,
                        'reason' => 'Недостатньо колонок',
                        'data' => $row
                    ];
                    continue;
                }

                $comment = $row[1] ?? '';
                $rawSystemRating = strtolower(trim($row[2] ?? ''));
                $rawExpertRating = strtolower(trim($row[3] ?? ''));
                $notes = $row[5] ?? '';

                // Системна оцінка вже є чистою емоцією
                $systemRating = $rawSystemRating;

                // Мапінг українських емоцій на англійські
                $ukrainianToEnglishEmotions = [
                    'радість' => 'joy',
                    'позитивний' => 'joy',
                    'сум' => 'sadness',
                    'відраза' => 'disgust',
                    'враження' => 'surprise',
                    'нейтрально' => 'neutral',
                    'злість' => 'anger',
                    'страх' => 'fear',
                    'захоплення' => 'joy'
                ];

                $expertRating = $ukrainianToEnglishEmotions[$rawExpertRating] ?? $rawExpertRating;

                if (!in_array($systemRating, $emotions)) {
                    $invalidRows[] = [
                        'row' => $rowIndex + 2,
                        'reason' => 'Невірна системна оцінка: ' . $rawSystemRating . ' (розпізнано: ' . $systemRating . ')',
                        'data' => $row
                    ];
                    continue;
                }

                if (!in_array($expertRating, $emotions)) {
                    $invalidRows[] = [
                        'row' => $rowIndex + 2,
                        'reason' => 'Невірна експертна оцінка: ' . $rawExpertRating . ' (розпізнано: ' . $expertRating . ')',
                        'data' => $row
                    ];
                    continue;
                }

                $total++;
                if ($systemRating === $expertRating) {
                    $correct++;
                }

                $confusionMatrix[$expertRating][$systemRating]++;

                if ($systemRating !== $expertRating) {
                    $errorAnalysis[$expertRating]['misclassifications'][$systemRating]++;
                    
                    if (count($errorAnalysis[$expertRating]['examples']) < 5) {
                        $errorAnalysis[$expertRating]['examples'][] = [
                            'comment' => $comment,
                            'system' => $systemRating,
                            'expert' => $expertRating,
                            'notes' => $notes
                        ];
                    }
                }
            }

            if ($total === 0) {
                Log::error('No valid data found', [
                    'invalid_rows' => $invalidRows,
                    'total_rows' => count($data)
                ]);
                
                $errorMessage = 'Не знайдено валідних даних у файлі. ';
                if (!empty($invalidRows)) {
                    $errorMessage .= 'Знайдено помилки в рядках: ' . implode(', ', array_column($invalidRows, 'row'));
                }
                return ['error' => $errorMessage];
            }

            // Розрахунок метрик
            $accuracy = $correct / $total;
            
            $classCounts = array_fill_keys($emotions, 0);
            foreach ($confusionMatrix as $actualEmotion => $predictedEmotions) {
                foreach ($predictedEmotions as $predictedEmotion => $count) {
                    if ($actualEmotion === $predictedEmotion) {
                        $classCounts[$actualEmotion] += $count;
                    }
                }
            }

            $metricsData = [];
            $macroF1Sum = 0;
            $weightedF1Sum = 0;
            $totalSamples = array_sum($classCounts);

            foreach ($emotions as $emotion) {
                $truePositives = $confusionMatrix[$emotion][$emotion];
                $falsePositives = array_sum(array_column($confusionMatrix, $emotion)) - $truePositives;
                $falseNegatives = array_sum($confusionMatrix[$emotion]) - $truePositives;

                $precision = ($truePositives + $falsePositives) > 0 ? 
                    $truePositives / ($truePositives + $falsePositives) : 0;
                $recall = ($truePositives + $falseNegatives) > 0 ? 
                    $truePositives / ($truePositives + $falseNegatives) : 0;
                $f1Score = ($precision + $recall) > 0 ? 
                    2 * ($precision * $recall) / ($precision + $recall) : 0;

                $metricsData[$emotion] = [
                    'precision' => $precision,
                    'recall' => $recall,
                    'f1_score' => $f1Score,
                    'support' => $classCounts[$emotion] ?? 0
                ];

                $macroF1Sum += $f1Score;
                $weightedF1Sum += $f1Score * ($classCounts[$emotion] / $totalSamples);
            }

            $macroF1 = count($emotions) > 0 ? $macroF1Sum / count($emotions) : 0;
            $weightedF1 = $totalSamples > 0 ? $weightedF1Sum : 0;

            $metrics = [
                'total' => $total,
                'correct' => $correct,
                'accuracy' => $accuracy,
                'macro_f1' => $macroF1,
                'weighted_f1' => $weightedF1,
                'confusion_matrix' => $confusionMatrix,
                'error_analysis' => $errorAnalysis,
                'metrics_by_emotion' => $metricsData
            ];

            Log::info('Metrics calculated successfully', [
                'total_rows' => $total,
                'accuracy' => $accuracy
            ]);

            return $metrics;
        } catch (\Exception $e) {
            Log::error('Error processing Excel file', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['error' => 'Помилка обробки файлу: ' . $e->getMessage()];
        }
    }
} 