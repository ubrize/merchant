<?php

namespace Arbory\Merchant;

use Illuminate\Support\ServiceProvider;

class PaymentsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');
        $this->publishes([
            __DIR__ . DIRECTORY_SEPARATOR .'Config'. DIRECTORY_SEPARATOR .'arbory-merchant.php' => config_path('arbory-merchant.php')
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PaymentsService::class, function ($app) {
            return new PaymentsService();
        });

        //  $this->mergeConfigFrom(
        //      __DIR__ . '/Config/arbory-merchant.php', 'arbory-merchant'
        //  );
    }
}
