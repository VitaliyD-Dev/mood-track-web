<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmotionAnalysesTable extends Migration
{
    public function up()
    {
        Schema::create('emotion_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('input_text'); // Вхідний текст
            $table->string('dominant_emotion'); // Основна емоція
            $table->decimal('confidence', 5, 4); // Впевненість у визначенні емоції
            $table->json('sentence_analysis'); // JSON з аналізом кожного речення
            $table->json('overall_emotions'); // JSON з підсумковим розподілом емоцій
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('emotion_analyses');
    }
}

