<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Request $request)
    {
        // awsでhttps化したらコメントを外す
        // 本番環境ならhttpsにする
        if (config('app.env') === 'production') {URL::forceScheme('https');}

        // 利用するクッキーをUrlで分ける
        $url = explode('/', $request->path() );
        if ($url[1] == 'extended') {
            config(['session.cookie' => config('session.cookie_'.'extednded')]);
        } else {
            config(['session.cookie' => config('session.cookie')]);
        }
    }
}
