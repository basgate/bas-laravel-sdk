<?php

namespace Bas\LaravelSdk;

use Bas\LaravelSdk\Services\BasService;
use Bas\LaravelSdk\Services\EncryptionService;
use Illuminate\Support\ServiceProvider;

class BasServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(BasService::class, function ($app) {
            return new BasService($app->make(EncryptionService::class));
        });

        $this->app->bind('bas', function () {
            return new BasService(app(EncryptionService::class));
        });

//        $this->app->singleton(BasService::class, function ($app) {
//            //dd(config('bas'));
//            return new BasService();
//        });
//
//        $this->app->bind('bas',function(){
//            return new BasService();
//        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/bas.php', 'bas');

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'bas');
//        $this->publishes([__DIR__.'/Config/bas.php' => config_path('bas.php'),], 'bas');
    }
}
