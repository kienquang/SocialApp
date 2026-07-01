<?php

namespace App\Http\Resources;

use Illuminate\Http\Request; // Import Request
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * Sửa chữ ký hàm để tương thích với Laravel 8/9
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $userVote = 0; // Mặc định là 0 (chưa vote (bầu chọn)/khách)

        // 1. Kiểm tra xem quan hệ (relationship) 'votes' (các phiếu bầu) có được tải (load) không
        //    (Controller (Bộ điều khiển) *nên* đã tải (load) nó)
        if ($this->relationLoaded('votes')) {

            // 2. 'votes' (các phiếu bầu) là một Collection (Danh sách) (đã được Controller (bộ điều khiển) lọc (filter)
            //    chỉ chứa vote (phiếu bầu) của user (người dùng) hiện tại). Lấy phần tử đầu tiên.
            $firstVote = $this->votes->first();

            // 3. Nếu nó không null (tức là user (người dùng) này đã vote (bầu chọn))
            if ($firstVote) {
                // Lấy giá trị 'vote' (không cần 'pivot')
                $userVote = (int) $firstVote->vote;
            }
        }

        // (LOGIC (LOGIC) MỚI) --- XỬ LÝ (HANDLE) IS_FOLLOWING_AUTHOR (TRẠNG THÁI THEO DÕI TÁC GIẢ) ---
        $isFollowingAuthor = $this->is_following_author; // Lấy (Get) thuộc tính "ảo" (virtual)
        // Nếu thuộc tính (property) không tồn tại (exist) (ví dụ: khi gọi (call) từ `index`), mặc định (default) là false (sai)
        if ($isFollowingAuthor === null) {
            $isFollowingAuthor = false;
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'thumbnail_url' => $this->optimizeUrl($this->thumbnail_url),
            'content_html' => $this->when($request->routeIs('posts.show'), $this->content_html), // Chỉ hiển thị content khi xem chi tiết
            'category' => new CategoryResource($this->whenLoaded('category')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            //  Chỉ hiển thị status cho Admin/Mod nếu nó KHÁC 'published'
            'status' => $this->when(
                $this->status !== 'published',
                $this->status
            ),

            // Thông tin tác giả (lồng UserResource)
            'author' => new UserResource($this->whenLoaded('user')),

            // Đếm (từ withCount)
            // 'comments_count' đã được đổi tên trong controller
            'comments_count' => $this->comments_count ?? 0,

            // Điểm vote (từ withSum)
            // 'vote_score' đã được đổi tên trong controller
            'vote_score' => (int) ($this->vote_score ?? 0),

            // Trạng thái vote của user hiện tại
            'user_vote' => (int) $userVote, // 1, -1, hoặc 0

            //Trạng thái (Status) theo dõi (follow) của user (người dùng) hiện tại đối với tác giả (author)
            'is_following_author' => (bool) $isFollowingAuthor,

        ];
    }
    private function optimizeUrl($url)
    {
        if (!$url) {
            return null;
        }
        // (SỬA) Đổi (Change) thành 'low' (thấp nhất)
        $transformations = 'q_auto:low,f_auto';
        return str_replace('/upload/', '/upload/' . $transformations . '/', $url);
    }
}
