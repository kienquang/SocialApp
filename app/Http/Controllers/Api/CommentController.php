<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CommentController extends Controller
{
    /**
     * API để lấy các bình luận phản hồi của 1 bình luận cha.
     * (GET /api/comments/{comment}/replies)
     */
    public function getReplies(Comment $comment)
    {
        // Tải các phản hồi (replies) của bình luận này,
        // đính kèm 'user' (tác giả) và 'replies_count' (cho cấp độ lồng nhau tiếp theo)
        $replies = $comment->replies()
                            ->with('user')
                            ->withCount('replies')
                            ->orderBy('created_at', 'asc') // Hiển thị từ cũ đến mới
                            ->paginate(5); // Phân trang, 5 trả lời mỗi trang

        return CommentResource::collection($replies);
    }

    /**
     * API để tạo một bình luận mới (gốc hoặc phản hồi).
     * (POST /api/comments)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            // Bài viết phải tồn tại
            'post_id' => ['required', Rule::exists('posts', 'id')],
            // Nếu parent_id được cung cấp, nó cũng phải tồn tại
            'parent_id' => ['nullable', Rule::exists('comments', 'id')],
        ]);

        /** @var \App\Models\User $user */ // <-- Thêm dòng này
        $user = Auth::user(); // <-- Gán user vào biến

        // Gọi 'posts()' từ biến $user
        $comment = $user->comments()->create($validated);

        // Tải 'user' ngay lập tức để trả về JSON cho đẹp
        $comment->load('user');

        return (new CommentResource($comment))
                ->response()
                ->setStatusCode(201); // 201 Created
    }

    /**
     * API để cập nhật bình luận.
     * (PATCH /api/comments/{comment})
     */
    public function update(Request $request, Comment $comment)
    {
        // 1. Kiểm tra quyền (sử dụng CommentPolicy)
        $this->authorize('update', $comment);

        // 2. Validate
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        // 3. Cập nhật
        $comment->update($validated);

        // Tải 'user' và 'replies_count' để trả về
        $comment->load('user')->loadCount('replies');

        return new CommentResource($comment);
    }

    /**
     * API để xóa bình luận.
     * (DELETE /api/comments/{comment})
     */
    public function destroy(Comment $comment)
    {
        // 1. Kiểm tra quyền (sử dụng CommentPolicy)
        $this->authorize('delete', $comment);

        // 2. Xóa
        $comment->delete();

        // 3. Trả về 204 No Content
        return response()->noContent();
    }
}
