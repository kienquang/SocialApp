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

    public function getBackground(){
        $config = Configuration::where('key','site_background')->first();

        $backgroundUrl = $config? $config->value: null;

        return response()->json([
            'background_url' => $backgroundUrl
        ]);
    }

    public function getFooter(){
        $keys = ['footer_description', 'footer_copyright', 'footer_links', 'footer_socials'];
        $configs = Configuration::whereIn('key', $keys)->pluck('value','key');

        return response()->json([
            'description'=>$configs['footer_description']??'',
            'copyright'   => $configs['footer_copyright'] ?? '',
            'links'       => json_decode($configs['footer_links'] ?? '[]'),
            'socials'     => json_decode($configs['footer_socials'] ?? '[]'),
        ]);
    }
}
