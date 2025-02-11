<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmotionAnalyzerController;

// Головний маршрут перенаправляє на emotion-analyzer
Route::get('/', function () {
    return redirect('/emotion-analyzer');
});

// Маршрути для аналізатора емоцій
Route::get('/emotion-analyzer', [EmotionAnalyzerController::class, 'index'])->name('emotion-analyzer');
Route::post('/emotion-analyzer', [EmotionAnalyzerController::class, 'analyze'])->name('analyze');

// Старий welcome route (можна видалити якщо не потрібен)
Route::get('/welcome', function () {
    return view('welcome');
});
