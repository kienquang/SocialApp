<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Report_comment;
use App\Models\Report_post;
use App\Models\Report_user;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\PostReportSent;
use App\Events\CommentReportSent;
use App\Events\UserReportSent;

class ReportController extends Controller
{
    /**
     * Bắt buộc phải đăng nhập để dùng các API này.
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Gửi báo cáo cho một Bài viết (Post).
     */
    public function storePostReport(Request $request, Post $post)
    {
        $validated = $request->validate([
            'reason' => 'required|string|min:10|max:500',
        ]);

        /** @var \App\Models\User $reporter */
        $reporter = Auth::user();

        // 1. Chỉ được báo cáo các bài viết 'published'
        if ($post->status !== 'published') {
            return response()->json(['message' => 'Nội dung này không tồn tại hoặc đã bị gỡ bỏ.'], 404);
        }

        // 2. Kiểm tra xem user đã báo cáo bài này chưa (tránh spam)
        $existing = Report_post::where('post_id', $post->id)
                                ->where('reporter_id', $reporter->id)
                                ->first();

        if ($existing) {
            return response()->json(['message' => 'Bạn đã báo cáo nội dung này rồi.'], 409); // 409 Conflict
        }

        // 3. Tạo báo cáo
        $report = Report_post::create([
            'post_id'     => $post->id,
            'reporter_id' => $reporter->id,
            'reason'      => $validated['reason'],
        ]);

        // Nếu PostReportSent đang mong id của post thì giữ nguyên $post->id
        // Nếu muốn dùng id bản ghi report_post thì đổi thành $report->id
        event(new PostReportSent($post->id, $reporter, $validated['reason'], $post));

        return response()->json(['message' => 'Báo cáo của bạn đã được gửi thành công.'], 201);
    }

    /**
     * Gửi báo cáo cho một Bình luận (Comment).
     */
    public function storeCommentReport(Request $request, Comment $comment)
    {
        $validated = $request->validate([
            'reason' => 'required|string|min:10|max:500',
        ]);

        /** @var \App\Models\User $reporter */
        $reporter = Auth::user();

        // 1. Chỉ được báo cáo các bình luận 'published'
        if ($comment->status !== 'published') {
            return response()->json(['message' => 'Nội dung này không tồn tại hoặc đã bị gỡ bỏ.'], 404);
        }

        // 2. Kiểm tra trùng lặp
        $existing = Report_comment::where('comment_id', $comment->id)
                                  ->where('reporter_id', $reporter->id)
                                  ->first();

        if ($existing) {
            return response()->json(['message' => 'Bạn đã báo cáo nội dung này rồi.'], 409);
        }

        // 3. Tạo báo cáo
        // ❗ Lưu lại bản ghi được tạo để lấy đúng id của report_comment
        $report = Report_comment::create([
            'comment_id'  => $comment->id,
            'reporter_id' => $reporter->id,
            'reason'      => $validated['reason'],
        ]);

        // ❗ Trước đây anh dùng $comment->id → sai: đó là id của comment
        // Đúng: truyền id của report_comment để admin dùng id này làm việc với bảng report_comments
        event(new CommentReportSent(
            $report->id,              // id của bản ghi report_comments
            $reporter,
            $validated['reason'],
            $comment                   // comment làm bằng chứng
        ));

        return response()->json(['message' => 'Báo cáo của bạn đã được gửi thành công.'], 201);
    }

    /**
     * Gửi báo cáo cho một Người dùng (User).
     */
    public function storeUserReport(Request $request, User $user)
    {
        $validated = $request->validate([
            'reason' => 'required|string|min:10|max:500',
        ]);

        /** @var \App\Models\User $reporter */
        $reporter = Auth::user();

        // 1. Không cho phép tự báo cáo
        if ($reporter->id === $user->id) {
            return response()->json(['message' => 'Bạn không thể báo cáo chính mình.'], 422);
        }

        // 2. Kiểm tra trùng lặp
        $existing = Report_user::where('reported_user_id', $user->id)
                               ->where('reporter_id', $reporter->id)
                               ->first();

        if ($existing) {
            return response()->json(['message' => 'Bạn đã báo cáo người dùng này rồi.'], 409);
        }

        // 3. Tạo báo cáo
        $report = Report_user::create([
            'reported_user_id' => $user->id,
            'reporter_id'      => $reporter->id,
            'reason'           => $validated['reason'],
        ]);

        // Tương tự PostReportSent, nếu UserReportSent đang dùng id user thì giữ $user->id
        // Nếu sau này anh muốn xử lý theo từng report_user thì có thể đổi sang $report->id
        event(new UserReportSent($user->id, $reporter, $validated['reason'], $user));

        return response()->json(['message' => 'Báo cáo của bạn đã được gửi thành công.'], 201);
    }
}
