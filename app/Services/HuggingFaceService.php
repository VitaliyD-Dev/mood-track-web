<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class HuggingFaceService
{
    protected $apiToken;
    protected $endpoint;

    public function __construct()
    {
        $this->apiToken = env('HF_API_TOKEN');

        $this->endpoint = 'https://api-inference.huggingface.co/models/mistralai/Mistral-7B-Instruct-v0.3';

    }

    public function sendPrompt(string $prompt, ?string $instruction = null): string
    {
        // Використати інструкцію, якщо вона задана
        // Якщо $instruction не передано (null), використовуємо стандартну інструкцію
        // Оператор ?? (null coalescing) повертає лівий операнд, якщо він не null, інакше - правий
        $instructionText = $instruction ?? "Ти — дружній помічник, який коротко й неформально відповідає українською.";

        $fullPrompt = "{$instructionText}\nКористувач:\n{$prompt}\nАсистент:";


        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken,
            'Content-Type' => 'application/json',
        ])->post($this->endpoint, [
                    'inputs' => $fullPrompt,
                    'parameters' => [
                        'temperature' => 0.3,
                        'max_new_tokens' => 250,
                        'top_p' => 0.9,
                        'repetition_penalty' => 1.1,
                    ],
                ]);

        if ($response->successful()) {
            $result = $response->json();

            if (isset($result[0]['generated_text'])) {
                $fullText = $result[0]['generated_text'];

                // Витягти відповідь після "Асистент:"
                $parts = explode('Асистент:', $fullText, 2);
                if (count($parts) === 2) {
                    return trim($parts[1]);
                }

                return trim($fullText);
            } elseif (isset($result['error'])) {
                return 'Помилка API: ' . $result['error'];
            }

            return 'Немає відповіді від моделі.';
        }

        return 'Помилка HTTP: ' . $response->body();
    }




}
