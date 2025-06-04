<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('video_analyses', function (Blueprint $table) {
            $table->text('video_description')->nullable()->after('video_title');
        });
    }

    public function down()
    {
        Schema::table('video_analyses', function (Blueprint $table) {
            $table->dropColumn('video_description');
        });
    }
}; 