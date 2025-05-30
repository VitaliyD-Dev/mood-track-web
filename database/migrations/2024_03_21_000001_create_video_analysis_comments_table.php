<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('video_analysis_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_analysis_id')->constrained()->onDelete('cascade');
            $table->string('author_name');
            $table->text('text');
            $table->string('emotion')->nullable();
            $table->timestamp('published_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('video_analysis_comments');
    }
}; 