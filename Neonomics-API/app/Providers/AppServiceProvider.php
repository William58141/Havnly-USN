<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Support\Client;
use App\Support\Request;
use GuzzleHttp\Client as GuzzleHttpClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('neonomics', function(){
            $guzzle = new GuzzleHttpClient([
                'base_url' => env('NEONOMICS_BASE_URL'),
                'timeout' => 2.0,
            ]);
            $request = new Request($guzzle);
            return new Client($request);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
