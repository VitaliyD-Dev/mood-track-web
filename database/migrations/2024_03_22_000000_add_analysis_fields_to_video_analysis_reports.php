<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('video_analysis_reports', function (Blueprint $table) {
            $table->json('communication_suggestions')->nullable()->after('audience_summary');
            $table->json('marketing_analysis')->nullable()->after('communication_suggestions');
            $table->json('content_inspection')->nullable()->after('marketing_analysis');
        });
    }

    public function down()
    {
        Schema::table('video_analysis_reports', function (Blueprint $table) {
            $table->dropColumn([
                'communication_suggestions',
                'marketing_analysis',
                'content_inspection'
            ]);
        });
    }
}; 