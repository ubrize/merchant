<?php

namespace Arbory\Payments;

use Illuminate\Support\ServiceProvider;

use Arbory\Payments\PaymentsService;

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
        $this->loadRoutesFrom(__DIR__.'/routes.php');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make(PaymentsController::class);
        $this->app->singleton(PaymentsService::class, function ($app) {
            return new PaymentsService();
        });
    }
}
