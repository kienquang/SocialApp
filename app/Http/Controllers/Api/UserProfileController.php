<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\File; // Sử dụng File rule

class UserProfileController extends Controller
{
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
        // 1. Validate file ảnh
        $validated = $request->validate([
            // 'file' là key mà frontend phải gửi lên
            // Tương tự ImageUploadController nhưng có thể nhẹ hơn
            'file' => [
                'required',
                File::image() // Quy tắc validate ảnh
                    ->max(2 * 1024), // Tối đa 2MB
            ],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 2. Tải ảnh lên Cloudinary
        try {
            // Tải file lên Cloudinary, nhưng lưu vào thư mục 'user_avatars'
            $uploadedFile = $validated['file']->storeOnCloudinary('user_avatars');

            $url = $uploadedFile->getSecurePath();

            // 3. CẬP NHẬT TRỰC TIẾP vào CSDL
            $user->update(['avatar' => $url]);

            // 4. Trả về URL mới (hoặc toàn bộ user object)
            return new UserResource($user);

        } catch (\Exception $e) {
            // Xử lý nếu upload thất bại
            return response()->json([
                'message' => 'Upload thất bại: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateCoverPhoto(Request $request)
    {
        $validated = $request->validate([
            'cover_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // Cho phép 5MB
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Tải (Upload) lên (on) Cloudinary, lưu vào thư mục (folder) 'user_covers'
        $uploadedFile = $validated['cover_photo']->storeOnCloudinary('user_covers');

        // Lấy URL (Đường link) an toàn
        $coverPhotoUrl = $uploadedFile->getSecurePath();

        // Cập nhật (Update) CSDL (Database)
        $user->update([
            'cover_photo_url' => $coverPhotoUrl,
        ]);

        // Trả về (Return) toàn bộ UserResource (Định dạng Người dùng) đã cập nhật (updated)
        return new UserResource($user);
    }
}
