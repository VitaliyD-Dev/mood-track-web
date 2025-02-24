<?php

namespace App\Http\Controllers;

use App\Services\EmotionAnalyzerService;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmotionAnalyzerController extends Controller
{
    protected $analyzerService;

    public function __construct(EmotionAnalyzerService $analyzerService)
    {
        $this->analyzerService = $analyzerService;
    }

    public function index()
    {
        return view('emotion.index');
    }

    public function analyze(Request $request)
    {
        $request->validate([
            'text' => 'required|string|min:3'
        ]);

        $result = $this->analyzerService->analyze($request->input('text'));

        return view('emotion.index', ['result' => $result]);
    }

    public function userIndex()
    {
        // Перевірка, чи користувач авторизований
        $user = Auth::user();

        if ($user) {
            // Оновлення часу останнього відвідування
            $user->last_seen = Carbon::now();
            $user->save(); // Зберігаємо зміни в базі даних
        }

        $users = User::all();
        return view('users.index', compact('users'));
    }

}
