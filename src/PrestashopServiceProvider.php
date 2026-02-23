<?php

namespace Nextpointer\Prestashop;

use Illuminate\Support\ServiceProvider;
use Nextpointer\Prestashop\Client\PrestashopClient;

class PrestashopServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/prestashop.php',
            'prestashop'
        );

        $this->app->singleton(PrestashopClient::class, function ($app) {
            return new PrestashopClient(
                config('prestashop.base_url'),
                config('prestashop.api_key'),
                config('prestashop.timeout', 15)
            );
        });

        $this->app->alias(PrestashopClient::class, 'prestashop');
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/prestashop.php' => config_path('prestashop.php'),
        ], 'prestashop-config');
    }
}