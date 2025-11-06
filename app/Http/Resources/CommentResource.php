<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // (LOGIC MỚI) Chỉ hiển thị status cho Admin/Mod nếu nó KHÁC 'published'
            'status' => $this->when(
                $this->status !== 'published',
                $this->status
            ),
            'parent_id' => $this->parent_id,

            // Đính kèm thông tin tác giả
            'author' => new UserResource($this->whenLoaded('user')),

            // Đếm số lượng phản hồi
            'replies_count' => $this->whenCounted('replies'),

            // Đính kèm các phản hồi (nếu được load)
            'replies' => CommentResource::collection($this->whenLoaded('replies')),
        ];
    }
}
