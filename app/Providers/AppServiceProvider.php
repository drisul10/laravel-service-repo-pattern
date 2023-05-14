<?php

namespace App\Providers;

use App\Repositories\SaleRepository;
use App\Repositories\SaleRepositoryImpl;
use Illuminate\Support\ServiceProvider;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryImpl;
use App\Repositories\VehicleRepository;
use App\Repositories\VehicleRepositoryImpl;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            UserRepository::class,
            UserRepositoryImpl::class,
        );

        $this->app->bind(
            SaleRepository::class,
            SaleRepositoryImpl::class,
        );

        $this->app->bind(
            VehicleRepository::class,
            VehicleRepositoryImpl::class,
        );
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
