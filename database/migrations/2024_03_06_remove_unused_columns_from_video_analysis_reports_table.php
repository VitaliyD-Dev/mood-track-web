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
        Schema::table('video_analysis_reports', function (Blueprint $table) {
            $table->dropColumn([
                'audience_summary',
                'communication_suggestions',
                'marketing_analysis'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('video_analysis_reports', function (Blueprint $table) {
            $table->json('audience_summary')->nullable();
            $table->json('communication_suggestions')->nullable();
            $table->json('marketing_analysis')->nullable();
        });
    }
}; 