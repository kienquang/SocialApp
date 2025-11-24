<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function getLogo()
    {
        // Tìm cấu hình có key là 'site_logo'
        $config = Configuration::where('key', 'site_logo')->first();

        // Nếu chưa có logo nào được set, trả về null hoặc một URL mặc định
        $logoUrl = $config ? $config->value : null;

        return response()->json([
            'logo_url' => $logoUrl
        ]);
    }
}
