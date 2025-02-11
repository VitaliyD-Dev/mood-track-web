<?php

namespace App\Http\Controllers;

use App\Services\EmotionAnalyzerService;
use Illuminate\Http\Request;

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
}
