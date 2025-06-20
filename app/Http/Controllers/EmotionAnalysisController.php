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

    public function index()
    {
        return view('emotion.index');
    }

    public function show($id)
    {
        $analysis = EmotionAnalysis::findOrFail($id);
        return view('emotion.show', compact('analysis'));
    }

    public function analyze(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:10000',
        ]);

        $text = $request->input('text');
        $userId = Auth::id();

        try {
            // Виконуємо аналіз
            $result = $this->emotionAnalyzer->analyze($text, $userId);

            // Якщо аналіз не вдався
            if (is_string($result)) {
                return redirect()->back()->with('error', 'Сталася помилка під час аналізу: ' . $result);
            }

            // Перевіряємо наявність всіх необхідних ключів
            if (!isset($result['dominant_emotion']) || !isset($result['confidence'])) {
                return redirect()->back()->with('error', 'Некоректний формат результатів аналізу');
            }

            // Збереження результатів аналізу в БД
            $analysis = EmotionAnalysis::create([
                'user_id' => $userId,
                'input_text' => $text,
                'dominant_emotion' => $result['dominant_emotion'],
                'confidence' => $result['confidence'],
                'sentence_analysis' => $result['sentence_analysis'] ?? [],
                'overall_emotions' => $result['overall_emotions'] ?? [],
            ]);

            return redirect()->route('emotion.history')->with('success', 'Аналіз виконано успішно.');
        } catch (\Exception $e) {
            \Log::error('Помилка при аналізі емоцій: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Сталася помилка під час аналізу. Будь ласка, спробуйте ще раз.');
        }
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
