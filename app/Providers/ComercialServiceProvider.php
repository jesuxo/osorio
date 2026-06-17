<?php
// app/Providers/ComercialServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;

class ComercialServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('*', function ($view) {
            $comercialId = Session::get('comercialid', 1);
            $view->with('comercialId', $comercialId);
        });
    }
}
