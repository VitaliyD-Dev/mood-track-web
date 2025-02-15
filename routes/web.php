<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmotionAnalyzerController;

// Головна сторінка для неавторизованих користувачів
Route::get('/', function () {
<<<<<<< HEAD
    return view('welcome');
});

// Маршрути для аналізатора емоцій
Route::get('/emotion-analyzer', [EmotionAnalyzerController::class, 'index'])->name('emotion-analyzer');
Route::post('/emotion-analyzer', [EmotionAnalyzerController::class, 'analyze'])->name('analyze');

// Старий welcome route (можна видалити якщо не потрібен)
Route::get('/welcome', function () {
    return view('welcome');
});

=======
    // Якщо користувач вже авторизований, перенаправляємо на dashboard
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    // Якщо ні - показуємо сторінку привітання
    return view('welcome');
})->name('welcome');

// Захищені маршрути
>>>>>>> 6a118078e090f9c2108a2fdc1bf96036064bb943
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
<<<<<<< HEAD
});

Route::get('/emotional-analyzer', [EmotionAnalyzerController::class, 'index'])->name('emotional-analyzer');
=======

    Route::get('/emotion-analyzer', [EmotionAnalyzerController::class, 'index'])
        ->name('emotion-analyzer');
    Route::post('/analyze', [EmotionAnalyzerController::class, 'analyze'])
        ->name('analyze');
});
>>>>>>> 6a118078e090f9c2108a2fdc1bf96036064bb943
