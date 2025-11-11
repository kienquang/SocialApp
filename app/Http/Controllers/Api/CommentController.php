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
    public function getForPost(Request $request, Post $post){
        $request-> validate([
            'limit' => 'sometimes|integer|min:1|max:50',
        ]);
        $limit = $request->query('limit', 10);
        $comments = $post->comments()
                        ->with('user')
                        ->withCount('replies as replies_count') // Đếm (Count) số phản hồi (replies)
                        ->orderBy('created_at', 'desc') // Mới nhất (Newest) lên trước (first)
                        ->paginate($limit);
        return CommentResource::collection($comments);
    }
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
        // 1. Kiểm tra quyền (Policy)
        $this->authorize('delete', $comment);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $newStatus = '';
        $message = '';

        // 2. Quyết định trạng thái (status) mới
        if ($user->role === 'moderator' || $user->role === 'admin' || $user->role === 'superadmin') {
            $newStatus = 'removed_by_mod';
            $message = 'Bình luận đã được gỡ bỏ bởi Ban Quản Trị.';
        } else {
            $newStatus = 'removed_by_author';
            $message = 'Bình luận đã được xóa bởi tác giả.';
        }

        // 3. Cập nhật status
        $comment->update([
            'status' => $newStatus
        ]);

        return response()->json(['message' => $message], 200);
    }
}
