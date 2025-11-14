<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Realtime\ChatController; 
use App\Http\Controllers\Realtime\NotificationController;
use App\Models\Notification;

//route chat
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/sendmessage',[ChatController::class,'sendMessage'])->name('sendmessage');

    Route::get('/conversations',[ChatController::class,'conversationList']);

    Route::get('/messages/{receiverId}',[ChatController::class,'fetchMessages']);

    Route::get('/test-realtime', function (Request $request) {
        return response()->json(['message' => 'Realtime route is working!']);
    });

    Route::post('/notifications/send', [NotificationController::class, 'send']);
});

//route notification    
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);

    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead']);
});
