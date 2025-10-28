<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    /**
     * Cho phép admin/superadmin làm mọi thứ
     */
    public function before(User $user, string $ability): bool|null
    {
        // Admin và Superadmin có thể bỏ qua mọi kiểm tra
        if ($user->role === 'admin' || $user->role === 'superadmin') {
            return true;
        }
        return null;
    }

    /**
     * Quyết định user có thể cập nhật comment không.
     * (Chỉ tác giả)
     */
    public function update(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id;
    }

    /**
     * Quyết định user có thể xóa comment không.
     * (Tác giả HOẶC Moderator)
     */
    public function delete(User $user, Comment $comment): bool
    {
        if ($user->role === 'moderator') {
            return true;
        }
        return $user->id === $comment->user_id;
    }
}
