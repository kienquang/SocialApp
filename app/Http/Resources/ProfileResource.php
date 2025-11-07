<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'avatar' => $this->optimizeUrl($this->avatar),
            'cover_photo_url' => $this->optimizeUrl($this->cover_photo_url),
            'role' => $this->role,
            'created_at' => $this->created_at,

            // Thêm (Add) các số đếm (counts) (đã được tải (load) từ Controller (Bộ điều khiển))
            'followers_count' => $this->followers_count ?? 0,
            'following_count' => $this->following_count ?? 0,
            'posts_count' => $this->posts_count ?? 0,

            // (MỚI) Thêm (Add) 'is_following' (trạng thái theo dõi) (đã được tính toán ở Controller (Bộ điều khiển))
            'is_following' => $this->is_following ?? false,
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
        // (SỬA) Đổi (Change) thành 'eco' (Tiết kiệm)
        $transformations = 'q_auto:eco,f_auto';
        return str_replace('/upload/', '/upload/' . $transformations . '/', $url);
    }
}
