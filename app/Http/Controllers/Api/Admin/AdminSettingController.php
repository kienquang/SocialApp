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

    public function updateBackground(Request $request){
        $validate = $request->validate([
            'file'=> 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $uploadedFile = Cloudinary::upload($validate['file']-> getRealPath(),[
            'folder'=> 'background_assets',
            'transformation'=>[
                'quality'=>'auto',
                'fetch_format'=>'auto'
            ]
        ]);

        $backgoundUrl = $uploadedFile->getSecurePath();
        Configuration::updateOrCreate(
                ['key' => 'site_background'],
                ['value' => $backgoundUrl]
            );

            return response()->json([
                'message' => 'Background đã được cập nhật thành công.',
                'background_url' => $backgoundUrl
            ]);
    }

    /**
     * (MỚI) Cập nhật thông tin Footer
     */
    public function updateFooter(Request $request)
    {
        // Validate dữ liệu
        $validated = $request->validate([
            'footer_description' => 'nullable|string|max:500',
            'footer_copyright'   => 'nullable|string|max:255',

            // Các liên kết footer (dạng mảng JSON)
            // Ví dụ: [{"label": "Về chúng tôi", "url": "/about"}]
            'footer_links'       => 'nullable|array',
            'footer_links.*.label' => 'required|string',
            'footer_links.*.url'   => 'required|string',

            // Các mạng xã hội (dạng mảng JSON)
            // Ví dụ: [{"platform": "facebook", "url": "..."}]
            'footer_socials'     => 'nullable|array',
            'footer_socials.*.platform' => 'required|string',
            'footer_socials.*.url'      => 'required|string',
        ]);

        // Lưu từng cái vào bảng configurations
        // 1. Description
        if (isset($validated['footer_description'])) {
            Configuration::updateOrCreate(['key' => 'footer_description'], ['value' => $validated['footer_description']]);
        }

        // 2. Copyright
        if (isset($validated['footer_copyright'])) {
            Configuration::updateOrCreate(['key' => 'footer_copyright'], ['value' => $validated['footer_copyright']]);
        }

        // 3. Links (Lưu dạng JSON string)
        if (isset($validated['footer_links'])) {
            Configuration::updateOrCreate(
                ['key' => 'footer_links'],
                ['value' => json_encode($validated['footer_links'])]
            );
        }

        // 4. Socials (Lưu dạng JSON string)
        if (isset($validated['footer_socials'])) {
            Configuration::updateOrCreate(
                ['key' => 'footer_socials'],
                ['value' => json_encode($validated['footer_socials'])]
            );
        }

        return response()->json(['message' => 'Footer đã được cập nhật thành công.']);
    }
}
