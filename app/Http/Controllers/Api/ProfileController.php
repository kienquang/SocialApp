<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Resources\ProfileResource; // (Chúng ta sẽ tạo file này ở Bước 2)
use App\Http\Resources\UserSearchResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
         * (MỚI) Tìm kiếm (Search) user (người dùng) theo tên.
         */
        public function search(Request $request)
        {
            $validated = $request->validate([
                'q' => 'required|string|min:2|max:100', // Bắt buộc phải có 'q'
                'limit' => 'sometimes|integer|min:1|max:20' // Giới hạn (Limit)
            ]);

            $searchTerm = $validated['q'];
            $limit = $validated['limit'] ?? 5; // Mặc định (Default) lấy 5

            $users = User::where('name', 'LIKE', '%' . $searchTerm . '%')
                        // Chỉ tìm user (người dùng) 'user', không tìm 'admin' (quản trị viên)
                        ->where('role', 'user')
                        ->limit($limit)
                        ->get();

            // Dùng Resource (Định dạng) "nhẹ" (lightweight) mới
            return UserSearchResource::collection($users);
        }
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

        // (ĐÃ SỬA) Thử (try) lấy user (người dùng) đang đăng nhập
        /** @var \App\Models\User|null $currentUser */
        $currentUser = Auth::guard('sanctum')->user();

        // (ĐÃ SỬA) Kiểm tra xem $currentUser (người dùng hiện tại) có đang theo dõi (follow) $user (người dùng) (trang profile) không
        $isFollowing = false;
        if ($currentUser) {
            // $user (người dùng) (trang profile) có $currentUser (người dùng hiện tại) trong danh sách 'followers' (người theo dõi) không
            $isFollowing = $user->followers()->where('follower_id', $currentUser->id)->exists();
        }

        // 1. Thêm (Add) 'is_following' (trạng thái theo dõi) như một thuộc tính "ảo" (virtual attribute) vào $user (người dùng)
        $user->is_following = $isFollowing;

        // 2. Trả về Resource (Định dạng) (ĐÃ XÓA ->additional())
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
