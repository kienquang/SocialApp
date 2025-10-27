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
