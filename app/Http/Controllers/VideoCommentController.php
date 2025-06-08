<?php

namespace App\Http\Controllers;

use App\Services\YouTubeService;
use App\Services\LLMAnalysisService;
use App\Models\VideoAnalysis;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class VideoCommentController extends Controller
{
    protected $youTubeService;
    protected $llmAnalysisService;

    public function __construct(YouTubeService $youTubeService, LLMAnalysisService $llmAnalysisService)
    {
        $this->youTubeService = $youTubeService;
        $this->llmAnalysisService = $llmAnalysisService;
    }

    public function showForm()
    {
        return view('video-comment-form');
    }

    public function fetchComments(Request $request)
    {
        $request->validate([
            'video_url' => 'required|url'
        ]);

        try {
            $videoId = $this->getVideoIdFromUrl($request->video_url);
            if (!$videoId) {
                return back()->withErrors(['video_url' => 'Неправильне посилання на відео YouTube']);
            }

            // Отримуємо деталі відео
            $videoInfo = $this->youTubeService->getVideoDetails($videoId);
            if (!$videoInfo) {
                return back()->withErrors(['error' => 'Відео не знайдено']);
            }

            // Отримуємо коментарі
            $youtubeCommentsData = $this->youTubeService->getVideoComments($videoId);
            $comments = $youtubeCommentsData['comments'];

            // Аналізуємо емоції коментарів
            $commentsWithEmotions = $this->youTubeService->analyzeCommentsEmotions($comments);

            // Підготовка даних для графіків
            $chartCommentsData = $this->prepareCommentsChartData($commentsWithEmotions);
            $emotionsData = $this->prepareEmotionsChartData($commentsWithEmotions);

            // Перевіряємо чи існує аналіз для цього відео
            $analysis = VideoAnalysis::where('video_id', $videoId)
                ->where('user_id', auth()->id())
                ->first();

            if ($analysis) {
                // Оновлюємо існуючий аналіз
                $analysis->update([
                    'video_title' => $videoInfo['snippet']['title'],
                    'comments_data' => $chartCommentsData,
                    'emotions_data' => $emotionsData,
                    'total_comments' => $youtubeCommentsData['totalResults']
                ]);

                // Видаляємо старі коментарі
                $analysis->comments()->delete();
            } else {
                // Створюємо новий аналіз
                $analysis = VideoAnalysis::create([
                    'user_id' => auth()->id(),
                    'video_id' => $videoId,
                    'video_title' => $videoInfo['snippet']['title'],
                    'comments_data' => $chartCommentsData,
                    'emotions_data' => $emotionsData,
                    'total_comments' => $youtubeCommentsData['totalResults']
                ]);
            }

            // Зберігаємо коментарі
            foreach ($commentsWithEmotions as $comment) {
                $analysis->comments()->create([
                    'author_name' => $comment['snippet']['topLevelComment']['snippet']['authorDisplayName'],
                    'text' => $comment['snippet']['topLevelComment']['snippet']['textDisplay'],
                    'emotion' => $comment['emotion'] ?? null,
                    'published_at' => $comment['snippet']['topLevelComment']['snippet']['publishedAt']
                ]);
            }

            // Генеруємо аналіз за допомогою Llama
            $analysisReport = $this->llmAnalysisService->analyzeComments($analysis);

            // Перенаправляємо на сторінку з коментарями
            return view('comments', [
                'videoId' => $videoId,
                'videoInfo' => $videoInfo,
                'comments' => $commentsWithEmotions,
                'commentsData' => $chartCommentsData,
                'emotionsData' => $emotionsData,
                'analysisReport' => $analysisReport
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching comments: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Помилка при отриманні коментарів: ' . $e->getMessage()]);
        }
    }

    public function show($videoId, Request $request)
    {
        try {
            // Перевіряємо чи існує аналіз для цього відео у поточного користувача
            $existingAnalysis = VideoAnalysis::where('video_id', $videoId)
                ->where('user_id', auth()->id())
                ->first();
            
            if ($existingAnalysis) {
                return redirect()->route('video-analysis.show', $existingAnalysis->id);
            }

            // Отримуємо деталі відео
            $videoInfo = $this->youTubeService->getVideoDetails($videoId);

            if (!$videoInfo) {
                return redirect()->route('video-comments.form')->withErrors(['error' => 'Відео не знайдено.']);
            }

            // Отримуємо коментарі
            $youtubeCommentsData = $this->youTubeService->getVideoComments($videoId);
            $comments = $youtubeCommentsData['comments'];

            // Аналізуємо емоції коментарів
            $commentsWithEmotions = $this->youTubeService->analyzeCommentsEmotions($comments);

            // Підготовка даних для графіків
            $chartCommentsData = $this->prepareCommentsChartData($commentsWithEmotions);
            $emotionsData = $this->prepareEmotionsChartData($commentsWithEmotions);

            // Створюємо новий аналіз
            $analysis = VideoAnalysis::create([
                'user_id' => auth()->id(),
                'video_id' => $videoId,
                'video_title' => $videoInfo['snippet']['title'],
                'comments_data' => $chartCommentsData,
                'emotions_data' => $emotionsData,
                'total_comments' => $youtubeCommentsData['totalResults']
            ]);

            // Зберігаємо коментарі
            foreach ($commentsWithEmotions as $comment) {
                $analysis->comments()->create([
                    'author_name' => $comment['snippet']['topLevelComment']['snippet']['authorDisplayName'],
                    'text' => $comment['snippet']['topLevelComment']['snippet']['textDisplay'],
                    'emotion' => $comment['emotion'] ?? null,
                    'published_at' => $comment['snippet']['topLevelComment']['snippet']['publishedAt']
                ]);
            }

            return redirect()->route('video-analysis.show', $analysis->id);
        } catch (\Exception $e) {
            \Log::error('Error in show method: ' . $e->getMessage());
            return redirect()
                ->route('video-comments.form')
                ->withErrors(['error' => 'Помилка при отриманні даних: ' . $e->getMessage()]);
        }
    }

    public function history()
    {
        $analyses = VideoAnalysis::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);
        return view('video-analysis.history', compact('analyses'));
    }

    public function showAnalysis($id)
    {
        $analysis = VideoAnalysis::with(['comments', 'latestAnalysisReport'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);
        
        // Підготовка даних для графіків
        $commentsData = collect($analysis->comments_data)->map(function($item) {
            return [
                'x' => strtotime($item['date']) * 1000,
                'y' => $item['count']
            ];
        })->values();

        $emotionsData = collect($analysis->emotions_data)->map(function($item) {
            $emotions = collect($item)->except('date')->map(function($count, $emotion) {
                return [
                    'name' => $emotion,
                    'value' => $count
                ];
            })->values();

            return [
                'date' => $item['date'],
                'emotions' => $emotions
            ];
        })->values();

        $emotionsLabels = ['joy', 'sadness', 'anger', 'fear', 'disgust', 'surprise', 'neutral'];

        // Пагінація коментарів
        $comments = $analysis->comments()->paginate(20);

        // Отримуємо останній звіт аналізу
        $analysisReport = $analysis->latestAnalysisReport;

        return view('video-analysis.show', compact('analysis', 'comments', 'commentsData', 'emotionsData', 'emotionsLabels', 'analysisReport'));
    }

    private function prepareCommentsChartData($comments)
    {
        $commentsByDate = [];
        
        foreach ($comments as $comment) {
            $date = \Carbon\Carbon::parse($comment['snippet']['topLevelComment']['snippet']['publishedAt'])->format('Y-m-d');
            if (!isset($commentsByDate[$date])) {
                $commentsByDate[$date] = 0;
            }
            $commentsByDate[$date]++;
        }

        ksort($commentsByDate);

        return array_map(function($date, $count) {
            return [
                'date' => $date,
                'count' => $count
            ];
        }, array_keys($commentsByDate), array_values($commentsByDate));
    }

    private function prepareEmotionsChartData($comments)
    {
        $emotionsByDate = [];
        
        foreach ($comments as $comment) {
            if (!isset($comment['emotion'])) continue;
            
            $date = \Carbon\Carbon::parse($comment['snippet']['topLevelComment']['snippet']['publishedAt'])->format('Y-m-d');
            if (!isset($emotionsByDate[$date])) {
                $emotionsByDate[$date] = [
                    'joy' => 0,
                    'sadness' => 0,
                    'anger' => 0,
                    'fear' => 0,
                    'disgust' => 0,
                    'surprise' => 0,
                    'neutral' => 0
                ];
            }
            $emotionsByDate[$date][$comment['emotion']]++;
        }

        ksort($emotionsByDate);

        return array_map(function($date, $emotions) {
            return array_merge(['date' => $date], $emotions);
        }, array_keys($emotionsByDate), array_values($emotionsByDate));
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
