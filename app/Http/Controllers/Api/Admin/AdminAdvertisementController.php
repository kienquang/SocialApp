<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdvertisementResource;
use App\Models\Advertisement;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;

class AdminAdvertisementController extends Controller
{
    public function index()
    {
        // Admin (Quản trị viên) cần xem (see) tất cả (all) (kể cả 'inactive' (không hoạt động))
        $ads = Advertisement::orderBy('position')->orderBy('display_order')->get();
        return AdvertisementResource::collection($ads);
    }

    /**
     * Lưu (Store) một quảng cáo (ad) mới.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'link_url' => 'required|url|max:500',
            'position' => 'required|string|max:100', // ví dụ: 'sidebar_top'
            'status' => 'required|string|in:active,inactive', // Chỉ 2 giá trị (values) này
            'display_order' => 'nullable|integer',
            'image_file' => 'required|image|max:2048', // Yêu cầu (Require) file (tệp) ảnh (image) (max 2MB)
        ]);

        try {
            // 1. Tải (Upload) ảnh (image) lên (on) Cloudinary
            $uploadedFile = Cloudinary::upload($request->file('image_file')->getRealPath(), [
                'folder' => 'advertisements',
                'transformation' => [
                    'quality' => 'auto:eco',
                    'fetch_format' => 'auto'
                ]
            ]);

            $imageUrl = $uploadedFile->getSecurePath();

            // 2. Tạo (Create) bản ghi (record) trong CSDL (database)
            $ad = Advertisement::create([
                'title' => $validated['title'],
                'link_url' => $validated['link_url'],
                'position' => $validated['position'],
                'status' => $validated['status'],
                'display_order' => $validated['display_order'] ?? 0,
                'image_url' => $imageUrl, // Lưu (Save) URL (Đường dẫn) Cloudinary
            ]);

            return (new AdvertisementResource($ad))
                   ->response()
                   ->setStatusCode(201);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Upload thất bại: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Cập nhật (Update) một quảng cáo (ad)
     */
    public function update(Request $request, Advertisement $advertisement)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'link_url' => 'sometimes|required|url|max:500',
            'position' => 'sometimes|required|string|max:100',
            'status' => 'sometimes|required|string|in:active,inactive',
            'display_order' => 'nullable|integer',
            'image_file' => 'nullable|image|max:2048', // Ảnh (Image) là tùy chọn (optional) khi update (cập nhật)
        ]);

        // (Logic (Logic) tương tự (similar) như 'store' (lưu), nhưng cho 'update' (cập nhật))
        try {
            // 1. Kiểm tra (Check) xem Admin (Quản trị viên) có tải (upload) ảnh (image) MỚI không
            if ($request->hasFile('image_file')) {
                 $uploadedFile = Cloudinary::upload($request->file('image_file')->getRealPath(), [
                    'folder' => 'advertisements',
                    'transformation' => [
                        'quality' => 'auto:eco',
                        'fetch_format' => 'auto'
                    ]
                ]);
                // (Nếu họ tải (upload) ảnh (image) mới, gán (assign) URL (Đường dẫn) mới)
                $validated['image_url'] = $uploadedFile->getSecurePath();
            }

            // 2. Cập nhật (Update) CSDL (database)
            $advertisement->update($validated);

            return new AdvertisementResource($advertisement);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Upload thất bại: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Xóa (Delete) một quảng cáo (ad).
     */
    public function destroy(Advertisement $advertisement)
    {
        // (Chúng ta có thể (can) Xóa Thật (Hard Delete) quảng cáo (ads),
        // vì chúng không phải là "bằng chứng" (evidence) như Posts (Bài viết))
        $advertisement->delete();
        return response()->noContent(); // 204
    }
}
