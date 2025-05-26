<?php

namespace App\Services;

use GuzzleHttp\Client;

class YouTubeService
{
    protected $apiKey;
    protected $client;

    public function __construct()
    {
        // Ваш API ключ
        $this->apiKey = env('YOUTUBE_API_KEY');
        $this->client = new Client();
    }

    public function getVideoDetails($videoId)
    {
        $url = "https://www.googleapis.com/youtube/v3/videos";
        $params = [
            'query' => [
                'key' => $this->apiKey,
                'id' => $videoId,
                'part' => 'snippet,statistics',
            ]
        ];

        $response = $this->client->get($url, $params);
        $data = json_decode($response->getBody(), true);

        if (isset($data['items'][0])) {
            return $data['items'][0];
        }

        return null;
    }

    // Функція для отримання коментарів
    public function getVideoComments($videoId, $pageToken = null)
    {
        $url = "https://www.googleapis.com/youtube/v3/commentThreads";
        $params = [
            'query' => [
                'key' => $this->apiKey,
                'videoId' => $videoId,
                'part' => 'snippet',
                'maxResults' => 100, // Increased to get more comments at once
                'order' => 'relevance',
            ]
        ];

        $response = $this->client->get($url, $params);
        $data = json_decode($response->getBody(), true);

        return [
            'comments' => $data['items'] ?? [],
            'totalResults' => $data['pageInfo']['totalResults'] ?? 0
        ];
    }

    // Аналіз емоцій для масиву коментарів
    public function analyzeCommentsEmotions(array $comments): array
    {
        $pythonPath = $this->detectPython();
        $scriptPath = base_path('python-scripts/analyze_emotions.py');

        if (!$pythonPath) {
            \Log::error('Python not found.');
            return $comments;
        }
        if (!file_exists($scriptPath)) {
            \Log::error('Python script not found.');
            return $comments;
        }

        // Підготовка даних для скрипта
        $commentsTexts = array_map(function($comment) {
            return $comment['snippet']['topLevelComment']['snippet']['textDisplay'];
        }, $comments);
        $payload = [
            'title' => $comments[0]['snippet']['videoTitle'] ?? '',
            'description' => '', // Якщо потрібно, додайте опис відео, якщо він є у вашій структурі
            'comments' => $commentsTexts,
        ];

        // 1. Створюємо тимчасовий файл з JSON
        $tmpFile = tempnam(sys_get_temp_dir(), 'yt_emotions_');
        file_put_contents($tmpFile, json_encode($payload, JSON_UNESCAPED_UNICODE));

        // 2. Формуємо команду для запуску Python-скрипта з шляхом до файлу
        $command = "{$pythonPath} {$scriptPath} " . escapeshellarg($tmpFile) . " 2>&1";

        // 3. Виконуємо команду
        $output = shell_exec($command);

        // 4. Видаляємо тимчасовий файл
        unlink($tmpFile);

        // 5. Далі все як було
        \Log::error('Python script output:', ['output' => $output]);
        if (!$output) {
            \Log::error('Failed to execute python script.');
            return $comments;
        }

        // 1. Знайти останній рядок, який є валідним JSON-масивом
        $lines = explode("\n", $output);
        $analysis = null;
        foreach (array_reverse($lines) as $line) {
            $line = trim($line);
            if (strpos($line, '[') === 0 && substr($line, -1) === ']') {
                $analysis = json_decode($line, true);
                break;
            }
        }
        if (!is_array($analysis)) {
            \Log::error('Invalid python script output.');
            return $comments;
        }

        // Додаємо емоції до коментарів
        foreach ($comments as &$comment) {
            $commentText = $comment['snippet']['topLevelComment']['snippet']['textDisplay'];
            foreach ($analysis as $item) {
                if ($item['comment'] === $commentText) {
                    $comment['emotion'] = $item['label'];
                    $comment['emotion_score'] = $item['score'];
                    break;
                }
            }
        }
        return $comments;
    }

    // Додаю функцію для визначення шляху до python
    private function detectPython()
    {
        $paths = [
            'C:\\Users\\vdobr\\anaconda3\\python.exe',
            'C:\\Users\\vdobr\\AppData\\Local\\Programs\\Python\\Python313\\python.exe',
            'C:\\Users\\vdobr\\AppData\\Local\\Microsoft\\WindowsApps\\python.exe',
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
