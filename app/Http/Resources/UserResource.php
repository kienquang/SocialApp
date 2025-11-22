<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        // Chỉ trả về thông tin public cơ bản của user
        return [
            'id' => $this->id,
            'name' => $this->name,
            'avatar' => $this->optimizeUrl($this->avatar, 'avatar'),
            'cover_photo_url' => $this->cover_photo_url,
            'created_at' => $this->created_at,
            'banned_until' => $this->when($this->is_banned, $this->banned_until),
        ];
    }

    /**
     * (MỚI) Hàm (Function) Helper (Hỗ trợ) Tối ưu (Optimize) URL (Đường dẫn) Cloudinary
     */
    private function optimizeUrl($url)
    {
        if (!$url) {
            return null;
        }

        // (SỬA) 'q_auto:low' là mức chất lượng thấp nhất của thuật toán tự động
        // Kết hợp 'f_auto' để chuyển sang định dạng nhẹ nhất (WebP/AVIF)
        $transformations = 'q_auto:low,f_auto';

        return str_replace('/upload/', '/upload/' . $transformations . '/', $url);
    }
}
