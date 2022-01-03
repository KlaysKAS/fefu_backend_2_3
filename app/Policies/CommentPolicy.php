<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    public function create(User $user) {
        return true;
    }

    public function update(User $user, Comment $comment) {
        return ((int)$user->role() === Role::MODERATOR || $user->id === $comment->user_id);
    }

    public function delete(User $user, Comment $comment) {
        return ((int)$user->role() === Role::MODERATOR || $user->id === $comment->user_id);
    }
}
