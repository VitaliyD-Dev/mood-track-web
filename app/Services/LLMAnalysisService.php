<?php

namespace App\Services;

use App\Models\VideoAnalysis;
use App\Models\VideoAnalysisReport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class LLMAnalysisService
{
    protected $ollamaUrl = 'http://localhost:11434';

    public function analyzeComments(VideoAnalysis $analysis)
    {
        try {
            // Prepare comments data for analysis
            $comments = $analysis->comments->map(function($comment) {
                return [
                    'text' => $comment->text,
                    'emotion' => $comment->emotion,
                    'published_at' => $comment->published_at
                ];
            })->toArray();

            Log::info('Analyzing comments with Llama: ' . json_encode($comments));

            // Generate only the requested types of analysis
            $emotionalOverview = $this->generateEmotionalOverview($comments);
            $topicalAnalysis = $this->generateTopicalAnalysis($comments);
            $controversialTopics = $this->generateControversialTopics($comments);
            $contentInspection = $this->generateContentInspection($comments);

            Log::info('Generated analysis: ' . json_encode([
                'emotional_overview' => $emotionalOverview,
                'topical_analysis' => $topicalAnalysis,
                'controversial_topics' => $controversialTopics,
                'content_inspection' => $contentInspection
            ]));

            // Save the analysis report
            return VideoAnalysisReport::create([
                'video_analysis_id' => $analysis->id,
                'emotional_overview' => $emotionalOverview,
                'topical_analysis' => $topicalAnalysis,
                'controversial_topics' => $controversialTopics,
                'content_inspection' => $contentInspection
            ]);

        } catch (\Exception $e) {
            Log::error('Llama Analysis failed: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function generateEmotionalOverview($comments)
    {
        try {
            $prompt = "Проаналізуй емоційний настрій коментарів та надай короткий огляд загального емоційного настрою аудиторії.\n\n";
            $prompt .= "Коментарі та їх емоції:\n";
            foreach ($comments as $comment) {
                $prompt .= "- {$comment['text']} (Емоція: {$comment['emotion']})\n";
            }
            $prompt .= "\nНадай детальний аналіз емоційного настрою аудиторії.";

            $response = $this->callLlama($prompt);
            Log::info('Generated emotional overview: ' . $response);
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to generate emotional overview: ' . $e->getMessage());
            return "Не вдалося згенерувати емоційний огляд.";
        }
    }

    protected function generateTopicalAnalysis($comments)
    {
        try {
            $prompt = "Проаналізуй теми коментарів та визнач, про що найчастіше коментують.\n\n";
            $prompt .= "Коментарі:\n";
            foreach ($comments as $comment) {
                $prompt .= "- {$comment['text']}\n";
            }
            $prompt .= "\nНадай детальний тематичний аналіз коментарів.";

            $response = $this->callLlama($prompt);
            Log::info('Generated topical analysis: ' . $response);
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to generate topical analysis: ' . $e->getMessage());
            return "Не вдалося згенерувати тематичний аналіз.";
        }
    }

    protected function generateControversialTopics($comments)
    {
        try {
            $prompt = "Вияви суперечливі чи полярні теми в коментарях.\n\n";
            $prompt .= "Коментарі:\n";
            foreach ($comments as $comment) {
                $prompt .= "- {$comment['text']} (Емоція: {$comment['emotion']})\n";
            }
            $prompt .= "\nНадай аналіз суперечливих та полярних тем.";

            $response = $this->callLlama($prompt);
            Log::info('Generated controversial topics: ' . $response);
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to generate controversial topics: ' . $e->getMessage());
            return "Не вдалося згенерувати аналіз суперечливих тем.";
        }
    }

    protected function generateContentInspection($comments)
    {
        try {
            $prompt = "Проаналізуй ці дані та коментарі і визнач:\n\n";
            $prompt .= "1. Чи містять коментарі мову ворожнечі, токсичність або особисті образи.\n";
            $prompt .= "2. Чи згадується щось політично, культурно або соціально чутливе.\n";
            $prompt .= "3. Чи є ризики для репутації бренду/автора.\n";
            $prompt .= "4. Чи може це відео спричинити хейт або токсичні обговорення.\n\n";
            $prompt .= "Коментарі:\n";
            foreach ($comments as $comment) {
                $prompt .= "- {$comment['text']} (Емоція: {$comment['emotion']})\n";
            }
            $prompt .= "\nНадай детальний аналіз потенційних ризиків та проблем.";

            $response = $this->callLlama($prompt);
            Log::info('Generated content inspection: ' . $response);
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to generate content inspection: ' . $e->getMessage());
            return "Не вдалося згенерувати аналіз контенту.";
        }
    }

    protected function callLlama($prompt)
    {
        try {
            $response = Http::timeout(30)->post($this->ollamaUrl . '/api/generate', [
                'model' => 'llama3',
                'prompt' => $prompt,
                'stream' => false
            ]);

            if ($response->failed()) {
                throw new \Exception('Failed to communicate with Llama: ' . $response->body());
            }

            $result = $response->json();
            return $result['response'] ?? 'Не вдалося отримати відповідь від моделі.';
        } catch (\Exception $e) {
            Log::error('Llama API call failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function generateWidgetSummary(VideoAnalysis $analysis)
    {
        $comments = $analysis->comments->map(function($comment) {
            return [
                'text' => $comment->text,
                'emotion' => $comment->emotion
            ];
        })->toArray();

        $prompt = $this->prepareCommentsForPrompt($comments);
        $instruction = "Надай дуже короткий (1-2 речення) огляд реакції аудиторії на відео. Зосередься на найважливішому.";
        
        return $this->huggingFace->sendPrompt($prompt, $instruction);
    }

    public function generateDetailedReport(VideoAnalysis $analysis)
    {
        $comments = $analysis->comments->map(function($comment) {
            return [
                'text' => $comment->text,
                'emotion' => $comment->emotion
            ];
        })->toArray();

        $prompt = $this->prepareCommentsForPrompt($comments);
        $instruction = "Надай детальний аналіз коментарів до відео. Включи:\n1. Детальний емоційний аналіз\n2. Розгорнутий тематичний аналіз\n3. Аналіз суперечливих тем\n4. Детальне резюме думок аудиторії\n5. Рекомендації щодо покращення контенту";
        
        return $this->huggingFace->sendPrompt($prompt, $instruction);
    }
} 