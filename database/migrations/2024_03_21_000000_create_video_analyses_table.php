<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('video_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('video_id');
            $table->string('video_title');
            $table->json('comments_data');
            $table->json('emotions_data');
            $table->integer('total_comments');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('video_analyses');
    }
}; 