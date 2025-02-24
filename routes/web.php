<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmotionAnalyzerController;

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

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::get('/emotional-analyzer', [EmotionAnalyzerController::class, 'index'])->name('emotional-analyzer');
Route::get('/users', [EmotionAnalyzerController::class, 'userIndex'])->middleware(['auth:sanctum', 'verified'])->name('users.index');

