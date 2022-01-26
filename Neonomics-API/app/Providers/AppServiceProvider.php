<?php

namespace App\Providers;

use App\Support\Client;
use App\Support\Request;
use GuzzleHttp\Client as GuzzleHttpClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('neonomics', function () {
            $guzzle = new GuzzleHttpClient([
                'base_uri' => env('NEONOMICS_BASE_URL') . '/' . env('NEONOMICS_PRODUCT') . '/' . env('NEONOMICS_API_VERSION') . '/'
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
