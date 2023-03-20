<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class NullAvoidanceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        app()->singleton('NullAvoidance', 'App\Tools\NullAvoidanceToolKit');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
