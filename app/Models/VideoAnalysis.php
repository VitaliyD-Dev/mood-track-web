<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoAnalysis extends Model
{
    protected $fillable = [
        'user_id',
        'video_id',
        'video_title',
        'comments_data',
        'emotions_data',
        'total_comments'
    ];

    protected $casts = [
        'comments_data' => 'array',
        'emotions_data' => 'array'
    ];

    public function comments(): HasMany
    {
        return $this->hasMany(VideoAnalysisComment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 