<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('video_analyses', function (Blueprint $table) {
            $table->json('video_statistics')->nullable();
        });

        Schema::table('video_analysis_comments', function (Blueprint $table) {
            $table->integer('like_count')->default(0);
        });
    }

    public function down()
    {
        Schema::table('video_analyses', function (Blueprint $table) {
            $table->dropColumn('video_statistics');
        });

        Schema::table('video_analysis_comments', function (Blueprint $table) {
            $table->dropColumn('like_count');
        });
    }
};