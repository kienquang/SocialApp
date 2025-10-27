<?php

use App\Http\Controllers\Api\Superadmin\UserRoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

require __DIR__.'/auth.php';

Route::middleware('auth:sanctum')->group(function () {

});

Route::middleware(['auth:sanctum', 'role:moderator'])
    ->prefix('moderator') // Tiền tố /api/moderator
    ->name('moderator.')
    ->group(function () {

});

Route::middleware(['auth:sanctum', 'role:admin'])
    ->prefix('admin') // Tiền tố /api/admin
    ->name('admin.')
    ->group(function () {

});

Route::middleware(['auth:sanctum', 'role:superadmin'])
    ->prefix('superadmin') // Tiền tố /api/superadmin
    ->name('superadmin.')
    ->group(function () {
        Route::patch('/users/{user}/role', [UserRoleController::class, 'updateRole'])
             ->name('users.updateRole');

});




