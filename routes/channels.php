<?php

use Illuminate\Support\Facades\Broadcast;
use App\Http\Resources\UserResource;
use App\Models\User;

Broadcast::channel('online', function ($user) {
    return $user ? new UserResource($user) : null;
});

Broadcast::channel('message.user.{user_id1}-{user_id2}', function (User $user, int $userId1, int $userId2) {
    return $user->id === $userId1 || $user->id === $userId2 ? $user : null;
});

Broadcast::channel('message.group.{group_id}', function (User $user, int $groupId) {
    return $user->groups->contains('id', $groupId) ? $user : null;
});
