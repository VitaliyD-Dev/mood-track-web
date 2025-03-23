<?php

namespace App\Services;

use App\Models\EmotionAnalysis;

class EmotionAnalyzerService
{
    protected $pythonPath;

    public function __construct()
    {
        $this->pythonPath = $this->detectPython();
    }

    public function analyze($text, $userId)
{
    $scriptPath = base_path('app/Services/python.py');

    if (!$this->pythonPath) {
        return "Error: Python not found.";
    }

    if (!file_exists($scriptPath)) {
        return "Error: Python script not found.";
    }

    $command = escapeshellcmd("{$this->pythonPath} {$scriptPath} " . escapeshellarg($text));
    $output = shell_exec($command);

    if (!$output) {
        return "Error: Failed to execute script.";
    }

    // Декодуємо результат
    $analysisData = json_decode($output, true);

    // Перевірка на відсутність домінуючої емоції
    if (!isset($analysisData['dominant_emotion']) || !$analysisData['dominant_emotion']) {
        $analysisData['dominant_emotion'] = 'neutral';  // Значення за замовчуванням
    }

    // Зберігаємо результат у БД
    $analysis = EmotionAnalysis::create([
        'user_id' => $userId,
        'input_text' => $text,
        'dominant_emotion' => $analysisData['dominant_emotion'],
        'confidence' => $analysisData['confidence'] ?? 0,
        'sentence_analysis' => $analysisData['sentence_analysis'] ?? [],
        'overall_emotions' => $analysisData['overall_emotions'] ?? [],
    ]);

    return $analysisData;
}



    private function detectPython()
    {
        $paths = [
            'C:\Users\vdobr\anaconda3\python.exe',
            'C:\Users\vdobr\AppData\Local\Programs\Python\Python313\python.exe',
            'C:\Users\vdobr\AppData\Local\Microsoft\WindowsApps\python.exe',
            '/usr/bin/python3',
            '/usr/local/bin/python3',
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }
}
