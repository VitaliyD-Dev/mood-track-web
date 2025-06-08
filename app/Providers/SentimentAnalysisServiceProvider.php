<?php

namespace App\Providers;

use App\Services\SentimentAnalysisService;
use Illuminate\Support\ServiceProvider;

class SentimentAnalysisServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(SentimentAnalysisService::class, function ($app) {
            return new SentimentAnalysisService();
        });
    }

    public function boot()
    {
        //
    }
} 