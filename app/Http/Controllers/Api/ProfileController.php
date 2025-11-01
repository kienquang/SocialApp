<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Resources\ProfileResource; // (Chúng ta sẽ tạo file này ở Bước 2)
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Hiển thị thông tin hồ sơ (profile) chính của người dùng.
     * Bao gồm: Tên, avatar, bio, và SỐ LƯỢNG (counts).
     */
    public function show(User $user)
    {
        // Tải (load) các SỐ LƯỢNG (counts)
        $user->loadCount(['followers', 'following', 'posts']);

        // (Rất quan trọng) Kiểm tra xem NGƯỜI DÙNG ĐANG ĐĂNG NHẬP
        // có đang theo dõi người dùng (profile) này không.

        // Tạo một thuộc tính 'is_following' (ảo)
        $user->is_following = false; // Mặc định là false (cho khách)

        if (Auth::check()) {
            /** @var \App\Models\User $authUser */
            $authUser = Auth::user();

            // Dùng exists() để kiểm tra nhanh
            $user->is_following = $authUser->following()->where('followed_id', $user->id)->exists();
        }

        // Trả về qua ProfileResource để format JSON
        return new ProfileResource($user);
    }

    /**
     * Hiển thị DANH SÁCH những người đang theo dõi (followers) người dùng này.
     */
    public function getFollowers(User $user)
    {
        // Lấy danh sách (đã phân trang)
        $followers = $user->followers()->paginate(15);

        // Dùng UserResource (cũ) để hiển thị danh sách người
        return UserResource::collection($followers);
    }

    /**
     * Hiển thị DANH SÁCH những người mà người dùng này đang theo dõi (following).
     */
    public function getFollowing(User $user)
    {
        // Lấy danh sách (đã phân trang)
        $following = $user->following()->paginate(15);

        // Dùng UserResource (cũ) để hiển thị danh sách người
        return UserResource::collection($following);
    }
}
