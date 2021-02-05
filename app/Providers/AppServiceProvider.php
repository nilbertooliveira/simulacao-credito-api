<?php

namespace App\Providers;

use App\Repositories\Contracts\SimulacaoRepositoryInterface;
use App\Repositories\SimulacaoRepository;
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
        $this->app->bind(SimulacaoRepositoryInterface::class,
            SimulacaoRepository::class
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
