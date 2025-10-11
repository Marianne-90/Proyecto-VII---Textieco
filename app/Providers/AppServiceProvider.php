<?php

namespace App\Providers;

<<<<<<< HEAD
use Illuminate\Support\Facades\URL;
=======
use App\Models\Order;
use App\Observers\OrderObserver;
>>>>>>> origin/main
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
<<<<<<< HEAD
        if (config('app.env') === 'production') {
        URL::forceScheme('https');
    }
=======
         Order::observe(OrderObserver::class);
>>>>>>> origin/main
    }
}
