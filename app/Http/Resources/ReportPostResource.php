<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportPostResource extends JsonResource
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
            'report_id' => $this->id,
            'reason' => $this->reason,
            'reported_at' => $this->created_at,

            // Lồng thông tin người báo cáo (đã được load)
            'reporter' => new UserResource($this->whenLoaded('reporter')),

            // Lồng "bằng chứng" (Post) (đã được load)
            // PostResource sẽ tự động thêm trường 'status' nếu nó là 'removed_...'
            'evidence_post' => new PostResource($this->whenLoaded('post')),
        ];
    }
}
