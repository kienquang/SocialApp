<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdvertisementResource extends JsonResource
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
            // (SỬA) Chỉ tối ưu (optimize) chất lượng (quality) / định dạng (format)
            'image_url' => $this->optimizeUrl($this->image_url),
            'link_url' => $this->link_url,
            'position' => $this->position,
            'status' => $this->status,
            'display_order'=> $this->display_order,
            'created_at' =>$this->created_at,
            'updated_at' => $this->updated_at,
            // (Chúng ta không cần (need) trả về (return) 'title' (tiêu đề) hoặc 'status' (trạng thái) cho public (công khai))
        ];
    }

    private function optimizeUrl($url)
    {
        if (!$url) {
            return null;
        }
        // Chỉ tối ưu (optimize) chất lượng (quality) (q_auto:eco) và định dạng (format) (f_auto)
        $transformations = 'q_auto:eco,f_auto';
        return str_replace('/upload/', '/upload/' . $transformations . '/', $url);
    }
}
