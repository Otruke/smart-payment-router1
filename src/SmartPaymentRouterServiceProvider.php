<?php

namespace Otolorinrufus\SmartPaymentRouter;

use Illuminate\Support\ServiceProvider;

class SmartPaymentRouterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // Publish the config file
        $this->publishes([
            __DIR__ . '/../config/smartpaymentrouter.php' => config_path('smartpaymentrouter.php'),
        ], 'config');
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        // Merge config for package
        $this->mergeConfigFrom(
            __DIR__ . '/../config/smartpaymentrouter.php',
            'smartpaymentrouter'
        );

        // Bind the SmartPaymentRouter class to the app container
        $this->app->singleton('smartpayment', function () {
            return new SmartPaymentRouter();
        });
    }
}
