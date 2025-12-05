<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Thêm middleware auth:sanctum để xác thực Bearer token
        Broadcast::routes(['middleware' => ['auth:sanctum']]);
<<<<<<< HEAD
=======

        // Thêm middleware auth:sanctum để xác thực Bearer token
        Broadcast::routes(['middleware' => ['auth:sanctum']]);
>>>>>>> 25697107e27a3727c59d988f7a60532d5454465e
        require base_path('routes/channels.php');
    }
}
