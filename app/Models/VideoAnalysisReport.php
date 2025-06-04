<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoAnalysisReport extends Model
{
    protected $fillable = [
        'video_analysis_id',
        'emotional_overview',
        'topical_analysis',
        'controversial_topics',
        'audience_summary'
    ];

    protected $casts = [
        'emotional_overview' => 'array',
        'topical_analysis' => 'array',
        'controversial_topics' => 'array',
        'audience_summary' => 'array'
    ];

    public function videoAnalysis()
    {
        return $this->belongsTo(VideoAnalysis::class);
    }

    public function getCommentsWithEmotions()
    {
        return $this->videoAnalysis->getCommentsWithEmotions();
    }
} 