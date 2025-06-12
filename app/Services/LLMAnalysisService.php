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
            Log::info('Starting emotional overview generation with comments count: ' . count($comments));
            
            if (empty($comments)) {
                Log::warning('No comments provided for emotional analysis');
                return "No comments available for emotional analysis.";
            }

            $prompt = "Analyze the emotional sentiment of comments and provide a brief overview of the general emotional mood of the audience.\n\n";
            $prompt .= "Comments and their emotions:\n";
            foreach ($comments as $comment) {
                $prompt .= "- {$comment['text']} (Emotion: {$comment['emotion']})\n";
            }
            $prompt .= "\nProvide a detailed analysis of the audience's emotional mood.";

            Log::info('Sending emotional analysis prompt to Llama');
            $response = $this->callLlama($prompt);
            Log::info('Received emotional overview response: ' . $response);
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to generate emotional overview: ' . $e->getMessage() . "\nStack trace: " . $e->getTraceAsString());
            return "Не вдалося згенерувати емоційний огляд.";
        }
    }

    protected function generateTopicalAnalysis($comments)
    {
        try {
            Log::info('Starting topical analysis generation with comments count: ' . count($comments));
            
            if (empty($comments)) {
                Log::warning('No comments provided for topical analysis');
                return "No comments available for topical analysis.";
            }

            $prompt = "Analyze the topics of comments and determine what people comment about most frequently.\n\n";
            $prompt .= "Comments:\n";
            foreach ($comments as $comment) {
                $prompt .= "- {$comment['text']}\n";
            }
            $prompt .= "\nProvide a detailed topical analysis of the comments.";

            Log::info('Sending topical analysis prompt to Llama');
            $response = $this->callLlama($prompt);
            Log::info('Received topical analysis response: ' . $response);
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to generate topical analysis: ' . $e->getMessage() . "\nStack trace: " . $e->getTraceAsString());
            return "Не вдалося згенерувати тематичний аналіз.";
        }
    }

    protected function generateControversialTopics($comments)
    {
        try {
            $prompt = "Identify controversial or polarizing topics in the comments.\n\n";
            $prompt .= "Comments:\n";
            foreach ($comments as $comment) {
                $prompt .= "- {$comment['text']} (Emotion: {$comment['emotion']})\n";
            }
            $prompt .= "\nProvide an analysis of controversial and polarizing topics.";

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
            $prompt = "Analyze this data and comments to determine:\n\n";
            $prompt .= "1. Whether the comments contain hate speech, toxicity, or personal insults.\n";
            $prompt .= "2. If anything politically, culturally, or socially sensitive is mentioned.\n";
            $prompt .= "3. If there are any risks to the brand/author's reputation.\n";
            $prompt .= "4. If this video could trigger hate or toxic discussions.\n\n";
            $prompt .= "Comments:\n";
            foreach ($comments as $comment) {
                $prompt .= "- {$comment['text']} (Emotion: {$comment['emotion']})\n";
            }
            $prompt .= "\nProvide a detailed analysis of potential risks and issues.";

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
            Log::info('Calling Llama API with prompt length: ' . strlen($prompt));
            
            $maxRetries = 3;
            $retryCount = 0;
            $lastException = null;

            while ($retryCount < $maxRetries) {
                try {
                    $response = Http::timeout(60)->retry(3, 1000)->post($this->ollamaUrl . '/api/generate', [
                        'model' => 'llama3',
                        'prompt' => $prompt,
                        'stream' => false,
                        'options' => [
                            'temperature' => 0.7,
                            'top_p' => 0.9,
                            'max_tokens' => 1000
                        ]
                    ]);

                    if ($response->failed()) {
                        Log::error('Llama API failed with status: ' . $response->status() . ' and body: ' . $response->body());
                        throw new \Exception('Failed to communicate with Llama: ' . $response->body());
                    }

                    $result = $response->json();
                    Log::info('Received Llama API response: ' . json_encode($result));
                    
                    if (!isset($result['response'])) {
                        Log::error('Invalid response format from Llama: ' . json_encode($result));
                        throw new \Exception('Не вдалося отримати відповідь від моделі.');
                    }
                    
                    return $result['response'];
                } catch (\Exception $e) {
                    $lastException = $e;
                    $retryCount++;
                    Log::warning("Attempt {$retryCount} failed: " . $e->getMessage());
                    
                    if ($retryCount < $maxRetries) {
                        sleep(2); // Wait 2 seconds before retrying
                        continue;
                    }
                }
            }

            // If we get here, all retries failed
            Log::error('All Llama API retry attempts failed: ' . $lastException->getMessage());
            throw $lastException;
        } catch (\Exception $e) {
            Log::error('Llama API call failed: ' . $e->getMessage() . "\nStack trace: " . $e->getTraceAsString());
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
        $instruction = "Provide a very brief (1-2 sentences) overview of the audience's reaction to the video. Focus on the most important aspects.";
        
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
        $instruction = "Provide a detailed analysis of the video comments. Include:\n1. Detailed emotional analysis\n2. Comprehensive topic analysis\n3. Analysis of controversial topics\n4. Detailed summary of audience opinions\n5. Recommendations for content improvement";
        
        return $this->huggingFace->sendPrompt($prompt, $instruction);
    }
} 