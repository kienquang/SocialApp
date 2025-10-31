<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    /**
     *Quyền xem các bài viết không public (ví dụ: status = 'removed_by_mod').
     * Đây là quyền "xem bằng chứng" của Admin/Mod.
     */
    public function view(User $user, Post $post): bool
    {
        // Chỉ Mod hoặc cao hơn mới có quyền xem các bài đã bị gỡ
        return $user->role === 'moderator'
            || $user->role === 'admin'
            || $user->role === 'superadmin';
    }
    /**
     * Kiểm tra xem user có thể cập nhật bài viết không.
     * Chỉ chủ sở hữu mới có quyền cập nhật.
     */
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    /**
     * Kiểm tra xem user có thể xóa bài viết không.
     * Chủ sở hữu HOẶC (moderator, admin, superadmin) có quyền xóa.
     */
    public function delete(User $user, Post $post): bool
    {
        // Định nghĩa cấp bậc (giống hệt CheckRole middleware)
        $roleHierarchy = [
            'user' => 1,
            'moderator' => 2,
            'admin' => 3,
            'superadmin' => 4,
        ];

        $userLevel = $roleHierarchy[$user->role] ?? 0;

        // Cho phép nếu là chủ sở hữu, HOẶC cấp bậc từ moderator trở lên
        return $user->id === $post->user_id || $userLevel >= 2;
    }
}
