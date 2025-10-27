<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'title' => $this->title,
            'content_html' => $this->content_html,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Đính kèm thông tin tác giả (dùng UserResource ở trên)
            // 'user' phải được eager-load (with('user')) trong Controller
            'author' => new UserResource($this->whenLoaded('user')),

            // Đếm số lượng bình luận (nếu có)
            // 'comments_count' phải được load (withCount('comments')) trong Controller
            'comments_count' => $this->whenCounted('comments'),

            // (Bạn có thể thêm 'comments' ở đây nếu là trang chi tiết)
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
        ];
    }
}
