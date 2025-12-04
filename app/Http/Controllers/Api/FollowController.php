<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\FollowedNotification;
use App\Jobs\StoreUserPostNotification;
use App\Models\Notification;
use Mockery\Matcher\Not;

class FollowController extends Controller
{
    /**
     * Xử lý hành động "Theo dõi" hoặc "Bỏ theo dõi" một người dùng.
     *
     * @param  \App\Models\User  $user (Đây là người dùng MÀ BẠN MUỐN theo dõi)
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleFollow(User $user)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        // 1. User không thể tự theo dõi chính mình
        if ($currentUser->id === $user->id) {
            return response()->json([
                'message' => 'Bạn không thể tự theo dõi chính mình.'
            ], 422); // 422 Unprocessable Entity
        }

        // 2. Lấy danh sách những người $currentUser đang theo dõi
        $following = $currentUser->following();

        // 3. Kiểm tra xem đã theo dõi chưa
        $isFollowing = $following->where('followed_id', $user->id)->exists();

        $message = '';
        $newIsFollowing = false;

        if ($isFollowing) {
            // --- KỊCH BẢN 1: Đã theo dõi -> Bỏ theo dõi ---
            $following->detach($user->id);
            $message = 'Đã bỏ theo dõi.';
            $newIsFollowing = false;
        } else {
            // --- KỊCH BẢN 2: Chưa theo dõi -> Theo dõi ---
            $following->attach($user->id);
            $message = 'Đã theo dõi thành công.';
            $newIsFollowing = true;

            $follow = [
                'follower_id' => $currentUser->id,
                'followed_id' => $user->id,
                'created_at' => now(),
                'sender' => [
                    'id' => $currentUser->id,
                    'name' => $currentUser->name,
                    'avatar' => $currentUser->avatar,
                ]
            ];
            $notification = Notification::create([
                'sender_id' => $currentUser->id,
                'type' => 'follow',
                'created_at' => now(),
            ]);
            event(new FollowedNotification((object)$follow));

            dispatch(new StoreUserPostNotification(
                $notification->id,
                $currentUser->id,
                null,
                null,
                'follow',
                [$user->id],
            ))->onQueue('notification');
        }

        // 4. Lấy tổng số người theo dõi MỚI của user kia
        $newFollowersCount = $user->followers()->count();

        // 5. Trả về response cho frontend
        return response()->json([
            'message' => $message,
            'is_following' => $newIsFollowing,           // Trạng thái MỚI (để frontend cập nhật nút)
            'followers_count' => $newFollowersCount,   // Tổng số người theo dõi MỚI (của người kia)
        ]);
    }
}
