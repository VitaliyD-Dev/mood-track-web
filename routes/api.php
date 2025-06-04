<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Llama 3 API routes
Route::prefix('llama')->group(function () {
    Route::post('/chat', [App\Http\Controllers\LlamaController::class, 'chat']);
    Route::get('/status', [App\Http\Controllers\LlamaController::class, 'status']);
});
