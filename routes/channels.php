<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Broadcast::routes() đã được gọi trong BroadcastServiceProvider, không cần gọi lại ở đây

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('converation.change.{id}', function ($user, $id) {
    return (int)$user->id === (int)$id;
});
/*Broadcast::channel('message.sent.{id}', function ($user, $id) {
    return (int)$user->id === (int)$id;
});*/
