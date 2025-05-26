<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\HuggingFaceService;

class LLMController extends Controller
{
    protected $huggingFace;

    public function __construct(HuggingFaceService $huggingFace)
    {
        $this->huggingFace = $huggingFace;
    }

    public function send(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
            'instruction' => 'nullable|string|max:2000',
        ]);

        $prompt = $request->input('prompt');
        $instruction = $request->input('instruction');

        $response = $this->huggingFace->sendPrompt($prompt, $instruction);

        return response()->json(['response' => $response]);
    }
}
