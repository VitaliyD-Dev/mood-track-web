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
            $table->text('input_text');
            $table->text('result');
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('emotion_analyses');
    }
}
