<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('emotion_analyses', function (Blueprint $table) {
            $table->string('dominant_emotion')->after('input_text'); // Основна емоція
            $table->decimal('confidence', 5, 4)->after('dominant_emotion'); // Впевненість
            $table->json('sentence_analysis')->after('confidence'); // Аналіз кожного речення
            $table->json('overall_emotions')->after('sentence_analysis'); // Підсумковий розподіл емоцій
        });
    }

    public function down()
    {
        Schema::table('emotion_analyses', function (Blueprint $table) {
            $table->dropColumn(['dominant_emotion', 'confidence', 'sentence_analysis', 'overall_emotions', 'result',]);
        });
    }
};
