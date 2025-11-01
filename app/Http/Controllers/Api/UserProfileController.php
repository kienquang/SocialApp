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
}
