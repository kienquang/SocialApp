<?php

use DeepCopy\f001\B;
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

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('conversation.change.{id}', function ($user, $id) {
    return (int)$user->id === (int)$id;
});
Broadcast::channel('notifications.{id}', function ($user, $id) {
    return (int)$user->id === (int)$id;
});
Broadcast::channel('comment.{postId}', function ($user, $postId) {
    return true;
});
/*Broadcast::channel('message.sent.{id}', function ($user, $id) {
    return (int)$user->id === (int)$id;
});*/

