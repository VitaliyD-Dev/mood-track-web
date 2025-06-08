<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('test_results', function (Blueprint $table) {
            $table->id();
            $table->integer('total_comments');
            $table->decimal('accuracy', 5, 2);
            $table->decimal('precision', 5, 2);
            $table->decimal('recall', 5, 2);
            $table->decimal('f1_score', 5, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('test_results');
    }
}; 