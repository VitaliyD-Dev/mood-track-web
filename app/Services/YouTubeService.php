<?php

namespace App\Services;

use GuzzleHttp\Client;

class YouTubeService
{
    protected $apiKey;

    public function __construct()
    {
        // Ваш API ключ
        $this->apiKey = env('YOUTUBE_API_KEY');
    }

    // Функція для отримання коментарів
    public function getVideoComments($videoId)
    {
        $client = new Client();
        
        $url = "https://www.googleapis.com/youtube/v3/commentThreads";
        $params = [
            'query' => [
                'key' => $this->apiKey,
                'videoId' => $videoId,
                'part' => 'snippet',
                'maxResults' => 100, // Максимум 100 коментарів на запит
            ]
        ];

        // Виконуємо запит
        $response = $client->get($url, $params);
        $data = json_decode($response->getBody(), true);

        // Якщо є коментарі, повертаємо їх
        if (isset($data['items'])) {
            return $data['items'];
        }

        return [];
    }
}
