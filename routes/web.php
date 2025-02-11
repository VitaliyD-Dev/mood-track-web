<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmotionAnalyzerController;

// Головна сторінка для неавторизованих користувачів
Route::get('/', function () {
    // Якщо користувач вже авторизований, перенаправляємо на dashboard
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    // Якщо ні - показуємо сторінку привітання
    return view('welcome');
})->name('welcome');

// Захищені маршрути
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/emotion-analyzer', [EmotionAnalyzerController::class, 'index'])
        ->name('emotion-analyzer');
    Route::post('/analyze', [EmotionAnalyzerController::class, 'analyze'])
        ->name('analyze');
});