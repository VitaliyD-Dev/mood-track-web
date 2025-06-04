<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('video_analysis_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_analysis_id')->constrained()->onDelete('cascade');
            $table->text('emotional_overview');
            $table->text('topical_analysis');
            $table->text('controversial_topics');
            $table->text('audience_summary');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('video_analysis_reports');
    }
}; 