<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmotionalBreakdown extends Model
{
    use HasFactory;

    // Вказуємо, які поля можна заповнювати
    protected $fillable = [
        'text',       // Текст, введений користувачем
        'analiz',     // Текстовий аналіз
        'chart_path', // Шлях до збереженого чарту
    ];

    // Встановлюємо типи полів для збереження у базі даних
    protected $casts = [
        'text' => 'string',
        'analiz' => 'string',
        'chart_path' => 'string',
    ];
}

