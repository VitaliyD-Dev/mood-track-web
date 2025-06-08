<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'total_comments',
        'accuracy',
        'precision',
        'recall',
        'f1_score'
    ];

    public function comments()
    {
        return $this->hasMany(TestComment::class);
    }
} 