<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmotionAnalyzerController;
use App\Http\Controllers\EmotionAnalysisController;
use App\Http\Controllers\VideoCommentController;




// Головна сторінка для неавторизованих користувачів
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
});

// Маршрути для аналізатора емоцій
Route::get('/emotion-analyzer', [EmotionAnalyzerController::class, 'index'])->name('emotion-analyzer');
Route::post('/emotion-analyzer', [EmotionAnalyzerController::class, 'analyze'])->name('analyze');

// Старий welcome route (можна видалити якщо не потрібен)
Route::get('/welcome', function () {
    return view('welcome');
});



Route::get('/emotional-analyzer', [EmotionAnalyzerController::class, 'index'])->name('emotional-analyzer');
Route::get('/users', [EmotionAnalyzerController::class, 'userIndex'])->middleware(['auth:sanctum', 'verified'])->name('users.index');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Тут також ми перевіряємо авторизацію, перед тим як виконати дії
    Route::post('/analyze', [EmotionAnalysisController::class, 'analyze'])->name('analyze');
    Route::get('/emotion-history', [EmotionAnalysisController::class, 'history'])->name('emotion.history');


    Route::get('/video-comments', [VideoCommentController::class, 'showForm'])->name('video-comments.form');
    Route::post('/video-comments', [VideoCommentController::class, 'fetchComments'])->name('video-comments.fetch');


});