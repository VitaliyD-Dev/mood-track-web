<?php

namespace App\Services;

use App\Models\VideoAnalysis;
use App\Models\VideoAnalysisReport;
use Illuminate\Support\Facades\Log;

class LLMAnalysisService
{
    protected $huggingFace;

    public function __construct(HuggingFaceService $huggingFace)
    {
        $this->huggingFace = $huggingFace;
    }

    public function analyzeComments(VideoAnalysis $analysis)
    {
        try {
            // Prepare comments data for analysis
            $comments = $analysis->comments->map(function($comment) {
                return [
                    'text' => $comment->text,
                    'emotion' => $comment->emotion,
                    'emotion_score' => $comment->emotion_score,
                    'emotions' => $comment->emotions
                ];
            })->toArray();

            Log::info('Analyzing comments with LLM: ' . json_encode($comments));

            // Generate different types of analysis
            $emotionalOverview = $this->generateEmotionalOverview($comments);
            $topicalAnalysis = $this->generateTopicalAnalysis($comments);
            $controversialTopics = $this->generateControversialTopics($comments);
            $audienceSummary = $this->generateAudienceSummary($comments);

            Log::info('Generated analysis: ' . json_encode([
                'emotional_overview' => $emotionalOverview,
                'topical_analysis' => $topicalAnalysis,
                'controversial_topics' => $controversialTopics,
                'audience_summary' => $audienceSummary
            ]));

            // Save the analysis report
            return VideoAnalysisReport::create([
                'video_analysis_id' => $analysis->id,
                'emotional_overview' => $emotionalOverview,
                'topical_analysis' => $topicalAnalysis,
                'controversial_topics' => $controversialTopics,
                'audience_summary' => $audienceSummary
            ]);

        } catch (\Exception $e) {
            Log::error('LLM Analysis failed: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function generateEmotionalOverview($comments)
    {
        try {
            $prompt = "Analyze the emotional content of these comments:\n\n";
            foreach ($comments as $comment) {
                $prompt .= "Comment: {$comment['text']}\n";
                $prompt .= "Emotion: {$comment['emotion']} (Score: {$comment['emotion_score']})\n";
                $prompt .= "Detailed emotions: " . json_encode($comment['emotions']) . "\n\n";
            }
            $prompt .= "Provide a comprehensive emotional overview of these comments.";

            $response = $this->huggingFace->generateText($prompt);
            Log::info('Generated emotional overview: ' . $response);
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to generate emotional overview: ' . $e->getMessage());
            return "Failed to generate emotional overview.";
        }
    }

    protected function generateTopicalAnalysis($comments)
    {
        try {
            $prompt = "Analyze the main topics discussed in these comments:\n\n";
            foreach ($comments as $comment) {
                $prompt .= "Comment: {$comment['text']}\n";
                $prompt .= "Emotion: {$comment['emotion']}\n\n";
            }
            $prompt .= "Identify and analyze the main topics discussed in these comments.";

            $response = $this->huggingFace->generateText($prompt);
            Log::info('Generated topical analysis: ' . $response);
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to generate topical analysis: ' . $e->getMessage());
            return "Failed to generate topical analysis.";
        }
    }

    protected function generateControversialTopics($comments)
    {
        try {
            $prompt = "Identify controversial topics in these comments:\n\n";
            foreach ($comments as $comment) {
                $prompt .= "Comment: {$comment['text']}\n";
                $prompt .= "Emotion: {$comment['emotion']}\n\n";
            }
            $prompt .= "Identify any controversial topics or points of contention in these comments.";

            $response = $this->huggingFace->generateText($prompt);
            Log::info('Generated controversial topics: ' . $response);
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to generate controversial topics: ' . $e->getMessage());
            return "Failed to generate controversial topics.";
        }
    }

    protected function generateAudienceSummary($comments)
    {
        try {
            $prompt = "Provide a summary of the audience's response based on these comments:\n\n";
            foreach ($comments as $comment) {
                $prompt .= "Comment: {$comment['text']}\n";
                $prompt .= "Emotion: {$comment['emotion']}\n\n";
            }
            $prompt .= "Provide a comprehensive summary of how the audience is responding to the content.";

            $response = $this->huggingFace->generateText($prompt);
            Log::info('Generated audience summary: ' . $response);
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to generate audience summary: ' . $e->getMessage());
            return "Failed to generate audience summary.";
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