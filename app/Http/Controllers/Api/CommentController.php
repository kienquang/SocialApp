<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Events\CommentNotification;
use App\Events\replyCommentNotification;
use App\Models\Notification;
use App\Models\UserNotification;
use App\Jobs\StoreUserPostNotification;
use Illuminate\Support\Facades\DB;

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
            'content'   => 'required|string',
            'post_id'   => 'required|integer',
            'parent_id' => 'nullable|integer',
        ]);
        $post = Post::findOrFail($validated['post_id']);
        $parentComment = null;
        if (!empty($validated['parent_id'])) {
            // findOrFail đảm bảo bình luận cha tồn tại
            $parentComment = Comment::findOrFail($validated['parent_id']);
        }

        /** @var \App\Models\User $user */ // <-- Thêm dòng này
        $user = Auth::user(); // <-- Gán user vào biến

        // Gọi 'posts()' từ biến $user
        $comment = $user->comments()->create($validated); //3

        // Tải 'user' ngay lập tức để trả về JSON cho đẹp
        //$comment->load('user'); thừa 1 truy vấn
        $comment->setRelation('user', $user); // Sử dụng setRelation để tránh truy vấn thừa

        // Phát sự kiện thông báo bình luận mới
        if ($comment->user_id !== $post->user_id) {
            // Tạo notification record
            $notification = Notification::create([
                'sender_id' => $user->id,
                'type' => 'comment',
                'post_id' => $post->id,
                'comment_id' => $comment->id,
                'created_at' => now(),
            ]);

            $cmt = [
                'id' => $comment->id,
                'post_id' => $comment->post_id,
                'parent_id' => $comment->parent_id,
                'user_id' => $comment->user_id,
                'user_name' => $comment->user->name,
                'author_id' => $post->user_id,
                'created_at' => $comment->created_at,
            ];
            event(new CommentNotification((object)$cmt));

            dispatch(new StoreUserPostNotification(
                $notification->id,
                $user->id,
                $post->id,
                $comment->id,
                'comment',
                [$post->user_id],
            ))->onQueue('notification');

            if ($comment->parent_id) {
                // Đây là phản hồi cho một bình luận khác
                // Gửi sự kiện replyCommentNotification
                if($parentComment->user_id != $comment->user_id) {
                    // Nếu người trả lời là chính chủ bình luận cha, không gửi thông báo
                $cmt['reply_to_user_id'] = $parentComment->user_id;
                event(new replyCommentNotification((object)$cmt));
                dispatch(new StoreUserPostNotification(
                    $notification->id,
                    $user->id,
                    $post->id,
                    $comment->id,
                    'reply_comment',
                    [$parentComment->user_id],
                ))->onQueue('notification');

                }
            }
        }


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
