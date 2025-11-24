<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use CloudinaryLabs\CloudinaryLaravel\MediaAlly;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\File; // Sử dụng File rule
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

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
     * (MỚI) Cập nhật (Update) mật khẩu (password) của user (người dùng)
     */
    public function updatePassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        // 1. Validate (Xác thực) (bao gồm (including) cả mật khẩu (password) mới)
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => [
                'required',
                'string',
                Password::defaults(), // Áp dụng (Apply) quy tắc (rule) phức tạp (mặc định (default) của Laravel)
                'confirmed', // Phải khớp (match) với 'password_confirmation'
                'different:current_password' // Mật khẩu (Password) mới phải khác (different) mật khẩu (password) cũ
            ],
        ]);

        // 2. (QUAN TRỌNG) Kiểm tra (Check) xem 'current_password' (mật khẩu hiện tại) có đúng không
        if (!Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Mật khẩu hiện tại không chính xác.'
            ]);
        }

        // 3. Cập nhật (Update) mật khẩu (password) mới
        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        // (Tùy chọn: Xóa (Delete) tất cả các token (mã) khác để bảo mật (security))
        // $user->tokens()->where('id', '!=', $request->user()->currentAccessToken()->id)->delete();

        return response()->json([
            'message' => 'Mật khẩu đã được cập nhật (updated) thành công.'
        ]);
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
                'quality' => 'auto:low',
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
