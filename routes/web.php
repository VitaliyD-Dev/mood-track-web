<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmotionAnalyzerController;
use App\Http\Controllers\EmotionAnalysisController;
use App\Http\Controllers\VideoCommentController;
use App\Http\Controllers\LLMController;
use App\Http\Controllers\TestingController;

// Головна сторінка для неавторизованих користувачів
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
});

// Старий welcome route (можна видалити якщо не потрібен)
Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/users', [EmotionAnalyzerController::class, 'userIndex'])->middleware(['auth:sanctum', 'verified'])->name('users.index');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Маршрути для аналізу емоцій
    Route::get('/emotion-analyzer', [EmotionAnalysisController::class, 'index'])->name('emotion.analyzer');
    Route::post('/analyze', [EmotionAnalysisController::class, 'analyze'])->name('analyze');
    Route::get('/emotion-history', [EmotionAnalysisController::class, 'history'])->name('emotion.history');
    Route::get('/emotion/{analysis}', [EmotionAnalysisController::class, 'show'])->name('emotion.show');

    // Маршрути для коментарів відео
    Route::get('/video-comments/form', [VideoCommentController::class, 'showForm'])->name('video-comments.form');
    Route::post('/video-comments/fetch', [VideoCommentController::class, 'fetchComments'])->name('video-comments.fetch');
    Route::get('/video-comments/{videoId}', [VideoCommentController::class, 'show'])->name('video-comments.show');
    Route::get('/video-comments/{videoId}/load', [VideoCommentController::class, 'loadComments'])->name('video-comments.load');

    // Video Analysis Routes
    Route::get('/video-analysis/history', [VideoCommentController::class, 'history'])->name('video-analysis.history');
    Route::get('/video-analysis/{id}', [VideoCommentController::class, 'showAnalysis'])->name('video-analysis.show');
    
    // Маршрут для чату
    Route::post('/chat/send', [LLMController::class, 'send']);

    // Тестування
    Route::get('/testing', [TestingController::class, 'index'])->name('testing');
    Route::post('/testing/upload', [TestingController::class, 'upload'])->name('testing.upload');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Інші захищені маршрути...
});

require __DIR__.'/auth.php';