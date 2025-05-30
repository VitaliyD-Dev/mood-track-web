<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoAnalysisComment extends Model
{
    protected $fillable = [
        'video_analysis_id',
        'author_name',
        'text',
        'emotion',
        'published_at'
    ];

    protected $casts = [
        'published_at' => 'datetime'
    ];

    public function analysis()
    {
        return $this->belongsTo(VideoAnalysis::class, 'video_analysis_id');
    }
} 