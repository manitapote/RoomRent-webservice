<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Roomrent\User\Repositories\UserRepositoryInterface',
        'Roomrent\User\Repositories\UserRepository');
        $this->app->bind('Roomrent\Posts\Repositories\PostRepositoryInterface',
        'Roomrent\Posts\Repositories\PostRepository');
    }
}
