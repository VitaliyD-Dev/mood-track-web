<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LlamaController extends Controller
{
    private $ollamaUrl = 'http://localhost:11434';

    public function chat(Request $request)
    {
        try {
            // Збільшуємо ліміт часу виконання PHP
            set_time_limit(300); // 5 хвилин

            // Валідація вхідних даних
            $request->validate([
                'message' => 'required|string',
                'model' => 'nullable|string'
            ]);

            // Перевірка доступності Ollama
            if (!$this->checkOllamaStatus()) {
                Log::error('Ollama is not running');
                return response()->json([
                    'error' => 'Ollama service is not running. Please start Ollama first.'
                ], 503);
            }

            $message = $request->input('message');
            $model = $request->input('model', 'llama3');

            // Розбиваємо повідомлення на менші частини, якщо це масив коментарів
            if (is_array($message)) {
                $batchSize = 10; // Обробляємо по 10 коментарів за раз
                $results = [];
                
                foreach (array_chunk($message, $batchSize) as $batch) {
                    $batchMessage = json_encode($batch);
                    
                    Log::info('Processing batch of comments', [
                        'batch_size' => count($batch)
                    ]);

                    $response = Http::timeout(120)->post($this->ollamaUrl . '/api/generate', [
                        'model' => $model,
                        'prompt' => $batchMessage,
                        'stream' => false,
                        'options' => [
                            'num_predict' => 2048,
                            'temperature' => 0.7,
                            'top_p' => 0.9,
                            'top_k' => 40
                        ]
                    ]);

                    if ($response->successful()) {
                        $result = $response->json();
                        if (isset($result['response'])) {
                            $results[] = $result['response'];
                        }
                    }

                    // Невелика пауза між батчами
                    usleep(500000); // 0.5 секунди
                }

                return response()->json([
                    'response' => $results
                ]);
            }

            // Звичайна обробка одиночного повідомлення
            Log::info('Sending request to Ollama', [
                'message' => $message,
                'model' => $model
            ]);

            $response = Http::timeout(120)->post($this->ollamaUrl . '/api/generate', [
                'model' => $model,
                'prompt' => $message,
                'stream' => false,
                'options' => [
                    'num_predict' => 2048,
                    'temperature' => 0.7,
                    'top_p' => 0.9,
                    'top_k' => 40
                ]
            ]);

            // Логування відповіді
            Log::info('Ollama response status', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->failed()) {
                Log::error('Ollama request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                return response()->json([
                    'error' => 'Failed to communicate with Ollama: ' . $response->body()
                ], 500);
            }

            $result = $response->json();
            
            if (!isset($result['response'])) {
                Log::error('Invalid response from Ollama', ['response' => $result]);
                return response()->json([
                    'error' => 'Invalid response from Ollama'
                ], 500);
            }

            return response()->json([
                'response' => $result['response']
            ]);

        } catch (\Exception $e) {
            Log::error('Error in LlamaController', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function status()
    {
        try {
            $response = Http::timeout(5)->get($this->ollamaUrl . '/api/tags');
            
            if ($response->successful()) {
                $models = $response->json();
                Log::info('Ollama status check successful', ['models' => $models]);
                
                return response()->json([
                    'status' => 'running',
                    'message' => 'Llama 3 API Server is running',
                    'models' => $models
                ]);
            }
            
            Log::error('Ollama status check failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Ollama is not responding correctly'
            ], 500);
            
        } catch (\Exception $e) {
            Log::error('Ollama status check error', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Ollama is not running: ' . $e->getMessage()
            ], 503);
        }
    }

    private function checkOllamaStatus()
    {
        try {
            $response = Http::timeout(5)->get($this->ollamaUrl . '/api/tags');
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Ollama status check failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
} 