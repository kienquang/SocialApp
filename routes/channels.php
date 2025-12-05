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

// Broadcast::routes() đã được gọi trong BroadcastServiceProvider, không cần gọi lại ở đây

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
Broadcast::channel('reports.post', function ($user) {
    return true;
});

Broadcast::channel('reports.comment', function ($user) {
    return true;
});

Broadcast::channel('reports.user', function ($user) {
    return $user->role === 'admin' || $user->role === 'superadmin';
});
