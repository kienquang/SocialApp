<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use CloudinaryLabs\CloudinaryLaravel\MediaAlly;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\File; // Sử dụng File rule

class UserProfileController extends Controller
{
    use MediaAlly;
    /**
         * (MỚI) Cập nhật (Update) các chi tiết (details) (như 'name' (tên)) của user (người dùng)
         */
        public function updateProfile(Request $request)
        {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Validate (Xác thực) (chỉ cho phép (allow) đổi (change) 'name' (tên))
            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:100',
                    // (Tùy chọn) Đảm bảo tên mới không trùng
                    // Rule (Quy tắc)::unique('users')->ignore($user->id),
                ],
                // (Sau này bạn có thể thêm (add) 'bio' (tiểu sử), 'location' (vị trí)... vào đây)
            ]);

            // Cập nhật (Update) user (người dùng)
            $user->update([
                'name' => $validated['name'],
            ]);

            // Trả về (Return) UserResource (Định dạng Người dùng) đã được cập nhật (update)
            return new UserResource($user);
        }
    /**
     * Cập nhật ảnh đại diện (avatar) của người dùng
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAvatar(Request $request)
    {
        // (SỬA) Dùng 'avatar' (ảnh đại diện) (từ file (tệp) 03) và `File::image()`
        $validated = $request->validate([
            'avatar' => [
                'required',
                File::image()
                    ->max(5 * 1024), // 5MB
            ],
        ]);

        /** @var \App\Models\User $user */
        $user = $request->user();

        try {
            // (SỬA) Dùng `storeOnCloudinary`
            $uploadedFile = $validated['avatar']->storeOnCloudinary('user_avatars', [
                // (THÊM LẠI) Logic (Logic) Tối ưu (Optimize)
                'transformation' => [
                    'quality' => 'auto:eco',
                    'fetch_format' => 'auto'
                ]
            ]);

            $url = $uploadedFile->getSecurePath();
            $user->update(['avatar' => $url]);

            // (LOGIC (LOGIC) CỦA BẠN) Trả về (Return) UserResource (Định dạng Người dùng)
            return new UserResource($user->fresh());

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Upload thất bại: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật (Update) ảnh bìa (cover photo) của user (người dùng)
     * (Dùng logic (logic) `storeOnCloudinary` của bạn)
     */
    public function updateCoverPhoto(Request $request)
    {
        // (SỬA) Dùng 'cover' (ảnh bìa) (từ file (tệp) 03)
        $validated = $request->validate([
            'cover' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB
        ]);

        /** @var \App\Models\User $user */
        $user = $request->user();

        // (SỬA) Dùng 'cover' (ảnh bìa)
        $uploadedFile = $validated['cover']->storeOnCloudinary('user_covers', [
             // (THÊM LẠI) Logic (Logic) Tối ưu (Optimize)
            'transformation' => [
                'quality' => 'auto:eco',
                'fetch_format' => 'auto'
            ]
        ]);

        $coverPhotoUrl = $uploadedFile->getSecurePath();

        $user->update([
            'cover_photo_url' => $coverPhotoUrl,
        ]);

        // (LOGIC (LOGIC) CỦA BẠN) Trả về (Return) UserResource (Định dạng Người dùng)
        return new UserResource($user->fresh());
    }
}
