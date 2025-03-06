<?php

namespace App\Http\Controllers;

use App\Services\YouTubeService;
use Illuminate\Http\Request;

class VideoCommentController extends Controller
{
    protected $youTubeService;

    public function __construct(YouTubeService $youTubeService)
    {
        $this->youTubeService = $youTubeService;
    }

    public function showForm()
    {
        return view('video-comment-form');
    }

    public function fetchComments(Request $request)
    {
        $videoUrl = $request->input('video_url');
        $videoId = $this->getVideoIdFromUrl($videoUrl);

        if (!$videoId) {
            return redirect()->back()->withErrors(['error' => 'Невірне посилання на відео.']);
        }

        // Отримуємо коментарі
        $comments = $this->youTubeService->getVideoComments($videoId);

        // Логіка для парсингу, збереження в БД, файл і т.д.

        return view('comments', ['comments' => $comments]);
    }

    private function getVideoIdFromUrl($url)
    {
        // Витягуємо ID відео з посилання
        preg_match('/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*\?v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches);
        return $matches[1] ?? null;
    }
}
