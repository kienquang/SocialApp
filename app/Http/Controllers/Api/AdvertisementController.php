<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdvertisementResource;
use App\Http\Resources\PublicAdvertismentResource;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Ramsey\Collection\Collection;

class AdvertisementController extends Controller
{
    /**
     * Lấy (Fetch) các quảng cáo (ads) "đang hoạt động" (active) cho Frontend (Giao diện)
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            // Yêu cầu (Require) Frontend (Giao diện) phải hỏi (ask) vị trí (position) cụ thể (specific)
            'position' => 'required|string|max:100'
        ]);

        $ads = Advertisement::where('status', 'active')
                            ->where('position', $validated['position'])
                            ->orderBy('display_order')
                            ->get();

        return PublicAdvertismentResource::collection($ads);
    }
}
