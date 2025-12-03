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
<<<<<<< HEAD
        Broadcast::routes(['middleware' => ['auth:sanctum']]);

=======
        // Thêm middleware auth:sanctum để xác thực Bearer token
        Broadcast::routes(['middleware' => ['auth:sanctum']]);
>>>>>>> origin/huyBranch2
        require base_path('routes/channels.php');
    }
}
