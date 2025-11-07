<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserSearchResource extends JsonResource
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
