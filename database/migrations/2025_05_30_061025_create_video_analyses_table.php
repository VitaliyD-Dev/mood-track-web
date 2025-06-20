<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('video_analyses', function (Blueprint $table) {
            $table->id();
            $table->string('video_id');
            $table->string('video_title');
            $table->json('comments_data');
            $table->json('emotions_data');
            $table->integer('total_comments');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_analyses');
    }
};
