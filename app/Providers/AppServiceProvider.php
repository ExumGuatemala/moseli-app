<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use App\Repositories\OrderRepository;
use App\Models\Order;
use Filament\Facades\Filament;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind(OrderRepository::class, function (Application $app) {
            return new OrderRepository($app->make(Order::class));
        });
        }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Filament::registerNavigationGroups([
            'Ventas',
            'Producción',
            'Administración',
        ]);
    }
}
