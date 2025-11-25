<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;

class AdminSettingController extends Controller
{
    public function updateLogo(Request $request)
    {
        // 1. Validate ảnh (Cho phép png, jpg, svg...)
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Max 2MB
        ]);

        try {
            // 2. Upload lên Cloudinary (Thư mục 'site_assets')
            $uploadedFile = Cloudinary::upload($request->file('logo')->getRealPath(), [
                'folder' => 'site_assets',
                'transformation' => [
                    'quality' => 'auto', // Logo cần nét nên để auto thay vì low
                    'fetch_format' => 'auto'
                ]
            ]);

            $logoUrl = $uploadedFile->getSecurePath();

            // 3. Lưu vào bảng configurations (Dùng updateOrCreate để không bị trùng)
            // Tìm dòng có key='site_logo', nếu có thì update, chưa có thì tạo mới
            Configuration::updateOrCreate(
                ['key' => 'site_logo'],
                ['value' => $logoUrl]
            );

            return response()->json([
                'message' => 'Logo đã được cập nhật thành công.',
                'logo_url' => $logoUrl
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Upload thất bại: ' . $e->getMessage()], 500);
        }
    }
}
