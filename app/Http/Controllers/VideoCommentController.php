<?php

namespace App\Http\Controllers;

use App\Services\YouTubeService;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

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
        try {
            $videoUrl = $request->input('video_url');
            
            if (!$videoUrl) {
                return redirect()->back()->withErrors(['error' => 'Будь ласка, введіть посилання на відео.']);
            }

            $videoId = $this->getVideoIdFromUrl($videoUrl);

            if (!$videoId) {
                return redirect()->back()->withErrors(['error' => 'Невірне посилання на відео.']);
            }

            return redirect()->route('video-comments.show', ['videoId' => $videoId]);
        } catch (\Exception $e) {
            \Log::error('Error fetching comments: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Помилка при обробці посилання: ' . $e->getMessage()]);
        }
    }

    public function show($videoId, Request $request)
    {
        try {
            // Отримуємо деталі відео
            $videoInfo = $this->youTubeService->getVideoDetails($videoId);

            if (!$videoInfo) {
                return redirect()->route('video-comments.form')->withErrors(['error' => 'Відео не знайдено.']);
            }

            // Отримуємо коментарі
            $commentsData = $this->youTubeService->getVideoComments($videoId);
            $comments = $commentsData['comments'];

            // Аналізуємо емоції коментарів
            $commentsWithEmotions = $this->youTubeService->analyzeCommentsEmotions($comments);

            return view('comments', [
                'videoId' => $videoId,
                'videoInfo' => $videoInfo,
                'comments' => $commentsWithEmotions,
                'totalResults' => $commentsData['totalResults']
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in show method: ' . $e->getMessage());
            return redirect()
                ->route('video-comments.form')
                ->withErrors(['error' => 'Помилка при отриманні даних: ' . $e->getMessage()]);
        }
    }

    private function getVideoIdFromUrl($url)
    {
        try {
            // Витягуємо ID відео з посилання
            if (preg_match('/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*\?v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches)) {
                return $matches[1];
            }
            return null;
        } catch (\Exception $e) {
            \Log::error('Error extracting video ID: ' . $e->getMessage());
            return null;
        }
    }

    public function loadComments($videoId, Request $request)
    {
        $pageToken = $request->get('pageToken');
        $commentsData = $this->youTubeService->getVideoComments($videoId, $pageToken);

        if ($request->ajax()) {
            $html = view('partials.comments', [
                'comments' => $commentsData['comments'],
                'videoId' => $videoId,
                'nextPageToken' => $commentsData['nextPageToken'],
                'prevPageToken' => $commentsData['prevPageToken']
            ])->render();

            return response()->json(['html' => $html]);
        }

        return redirect()->back();
    }
}
