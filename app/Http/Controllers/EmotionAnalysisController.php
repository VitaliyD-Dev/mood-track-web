<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\EmotionAnalyzerService;
use App\Models\EmotionAnalysis;
use App\Http\Controllers\Controller;

class EmotionAnalysisController extends Controller
{
    protected $emotionAnalyzer;

    public function __construct(EmotionAnalyzerService $emotionAnalyzer)
    {
        // Middleware тут більше не буде
        $this->emotionAnalyzer = $emotionAnalyzer;
    }

    public function analyze(Request $request)
{
    $request->validate([
        'text' => 'required|string|max:10000',
    ]);

    $text = $request->input('text');
    $userId = Auth::id(); // Отримуємо user_id

    // Виконуємо аналіз, передаючи userId
    $result = $this->emotionAnalyzer->analyze($text, $userId);

    return redirect()->route('emotion.history')->with('success', 'Аналіз виконано.');
}




    public function history()
    {
        $user = Auth::user();  // Авторизація тут
        if (!$user) {
            return redirect()->route('login');  // Якщо користувач не авторизований
        }

        $analyses = EmotionAnalysis::where('user_id', $user->id)->latest()->paginate(10);
        return view('emotion.history', compact('analyses'));
    }
}
