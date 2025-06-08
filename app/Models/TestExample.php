<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestExample extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment',
        'system_prediction',
        'expert_prediction',
        'category',
        'notes',
        'confidence_score'
    ];

    protected $casts = [
        'confidence_score' => 'float'
    ];
} 