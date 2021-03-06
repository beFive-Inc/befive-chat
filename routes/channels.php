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

Broadcast::channel('chatroom.{uuid}', function ($user, $uuid) {
    return $user;
});

Broadcast::channel('user.{id}', function ($user, $id) {
    return $user;
});

Broadcast::channel('chatroom.user.{uuid}', function ($user, $uuid) {
    return $user;
});
