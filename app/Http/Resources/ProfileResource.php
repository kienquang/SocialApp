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
            // Thông tin cơ bản
            'id' => $this->id,
            'name' => $this->name,
            'avatar' => $this->avatar,
            'role' => $this->role,
            'created_at' => $this->created_at,

            // SỐ LƯỢNG (Counts) - (Lấy từ loadCount)
            // (Dùng ?? 0 để an toàn nếu loadCount bị thiếu)
            'followers_count' => (int) ($this->followers_count ?? 0),
            'following_count' => (int) ($this->following_count ?? 0),
            'posts_count' => (int) ($this->posts_count ?? 0),

            // TRẠNG THÁI THEO DÕI (Rất quan trọng)
            // (Lấy từ thuộc tính 'is_following' (ảo) mà Controller đã tạo)
            // 'when' (khi) thuộc tính này tồn tại, hãy thêm vào
            'is_following' => $this->when(
                isset($this->is_following),
                (bool) $this->is_following,
                false // Giá trị mặc định (cho an toàn)
            ),
        ];
    }
}
