<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary; // <-- Giữ nguyên Facade (Giao diện) gốc của bạn

class ImageUploadController extends Controller
{
    /**
     * Xử lý việc upload (tải lên) ảnh từ trình soạn thảo (ví dụ: TinyMCE).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    // (SỬA) Đổi (Change) tên hàm (method) từ 'upload' -> 'store'
    // để khớp (match) với file 'routes/api.php' (file 03, 10:44 AM)
    public function upload(Request $request)
    {
        // 1. Validate (Xác thực) file (tệp) (Giữ nguyên)
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        try {
            // 2. Upload (Tải lên) file (tệp) LÊN Cloudinary VÀ Tối ưu (Optimize)
            $uploadedFile = Cloudinary::upload($request->file('file')->getRealPath(), [
                'folder' => 'laravel_posts', // Tên thư mục (folder) trên Cloudinary
                'resource_type' => 'image',

                // (MỚI) Áp dụng (Apply) biến đổi (transformations) ngay lúc upload (tải lên)
                'transformation' => [
                    [
                        'quality' => 'auto:low', // Tự động nén (compress) (low)
                        'fetch_format' => 'auto' // Tự động chọn (choose) định dạng (format) (webp, avif...)
                    ]
                ]
            ]);

            // 3. (SỬA LỖI) Lấy (Get) URL (Đường dẫn) an toàn (secure) (đã tối ưu (optimized))
            // (Thay vì gọi (call) Cloudinary::url())
            $optimizedUrl = $uploadedFile->getSecurePath();

            // 4. Trả về (Return) JSON (Định dạng Đối tượng JavaScript)
            return response()->json(['location' => $optimizedUrl], 200);

        } catch (\Exception $e) {
            // 5. Trả về (Return) lỗi (error)
            return response()->json(['message' => 'Upload thất bại: ' . $e->getMessage()], 500);
        }
    }
}
