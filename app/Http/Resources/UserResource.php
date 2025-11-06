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
            'avatar' => $this->avatar,
            'cover_photo_url' => $this->cover_photo_url,
            'created_at' => $this->created_at,
            'banned_until' => $this->when($this->is_banned, $this->banned_until),
        ];
    }
}
