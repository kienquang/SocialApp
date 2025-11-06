<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ImageUploadController extends Controller
{
    /**
     * Xử lý việc upload ảnh từ trình soạn thảo (ví dụ: TinyMCE).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        // 1. Validate file (phải là ảnh, dung lượng < 5MB)
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        try {
            // 2. Upload file lên Cloudinary
            // 'file' là tên key mà trình soạn thảo gửi lên
            $uploadedFile = Cloudinary::upload($request->file('file')->getRealPath(), [
                'folder' => 'laravel_posts', // Tên thư mục trên Cloudinary
                'resource_type' => 'image'
            ]);

            // 3. Lấy URL an toàn
            $secureUrl = $uploadedFile->getSecurePath();

            // 4. Trả về JSON theo định dạng mà TinyMCE/CKEditor mong đợi
            // Hầu hết các editor đều mong đợi một key là "location" hoặc "url"
            return response()->json(['location' => $secureUrl], 200);

        } catch (\Exception $e) {
            // 5. Trả về lỗi nếu có
            return response()->json(['message' => 'Upload thất bại: ' . $e->getMessage()], 500);
        }
    }
}
