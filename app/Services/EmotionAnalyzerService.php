<?php

namespace App\Services;

use App\Models\EmotionAnalysis;

class EmotionAnalyzerService
{
    protected $pythonPath;

    public function __construct()
    {
        $this->pythonPath = $this->detectPython();
    }

    public function analyze($text, $userId)
    {
        $scriptPath = base_path('app/Services/python.py');

        if (!$this->pythonPath) {
            return "Error: Python not found.";
        }

        if (!file_exists($scriptPath)) {
            return "Error: Python script not found.";
        }

        $command = escapeshellcmd("{$this->pythonPath} {$scriptPath} " . escapeshellarg($text));
        $output = shell_exec($command);

        if (!$output) {
            return "Error: Failed to execute script.";
        }

        // Зберігаємо результат у БД з user_id
        $analysis = EmotionAnalysis::create([
            'user_id' => $userId, // Додаємо user_id
            'input_text' => $text,
            'result' => $output
        ]);

        return $output;
    }


    private function detectPython()
    {
        $paths = [
            'C:\Users\vdobr\anaconda3\python.exe',
            'C:\Users\vdobr\AppData\Local\Programs\Python\Python313\python.exe',
            'C:\Users\vdobr\AppData\Local\Microsoft\WindowsApps\python.exe',
            '/usr/bin/python3',
            '/usr/local/bin/python3',
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }
}
