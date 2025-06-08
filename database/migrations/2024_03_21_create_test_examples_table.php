<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('test_examples', function (Blueprint $table) {
            $table->id();
            $table->text('comment');
            $table->string('system_prediction');
            $table->string('expert_prediction');
            $table->string('category');
            $table->text('notes')->nullable();
            $table->float('confidence_score')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('test_examples');
    }
}; 