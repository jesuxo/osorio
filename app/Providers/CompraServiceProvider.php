<?php
// app/Providers/CompraServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Compra\CompraProcessorService;
use App\Services\Serial\SerialTrackerService;

class CompraServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(CompraProcessorService::class, function ($app) {
            return new CompraProcessorService();
        });

        $this->app->singleton(SerialTrackerService::class, function ($app) {
            return new SerialTrackerService();
        });
    }

    public function boot()
    {
        //
    }
}
