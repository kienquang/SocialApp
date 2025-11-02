<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\ReportUserResource;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Report_user;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class UserManagementController extends Controller
{
    /**
     * (MỚI) Lấy lịch sử kiểm duyệt (danh sách sai phạm) của một người dùng.
     * Chỉ Admin mới có thể truy cập.
     */
    public function getModerationHistory(User $user)
    {
        // 1. Lấy BẰNG CHỨNG từ các bài viết (posts) đã bị Mod gỡ
        // Chúng ta KHÔNG cần 'withoutGlobalScope' vì 'PostController' (cũ) đã không dùng Global Scope
        $removedPosts = Post::where('user_id', $user->id)
                            ->where('status', 'removed_by_mod')
                            ->orderBy('updated_at', 'desc') // Sắp xếp theo ngày gỡ
                            ->get();

        // 2. Lấy BẰNG CHỨNG từ các bình luận (comments) đã bị Mod gỡ
        $removedComments = Comment::where('user_id', $user->id)
                                  ->where('status', 'removed_by_mod')
                                  ->orderBy('updated_at', 'desc')
                                  ->get();

        // 3. Lấy các BÁO CÁO (reports) đang hoạt động nhắm vào chính User này
        // (Ví dụ: Báo cáo về Avatar hoặc Tên)
        $activeUserReports = Report_user::where('reported_user_id', $user->id)
                                       ->with('reporter:id,name') // Tải (load) người báo cáo
                                       ->get();

        // 4. Trả về JSON
        return response()->json([
            'user_info' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'banned_until' => $user->banned_until,
            ],
            'violations' => [
                'removed_posts' => PostResource::collection($removedPosts),
                'removed_comments' => CommentResource::collection($removedComments),
                'active_user_reports' => ReportUserResource::collection($activeUserReports),
            ]
        ]);
    }
    /**
     * Ban (khóa) một người dùng theo thời gian.
     */
    public function ban(Request $request, User $user)
    {
        $validated = $request->validate([
            // 'duration_days' là tùy chọn, nếu không có sẽ ban vĩnh viễn (9999 năm)
            'duration_days' => 'nullable|integer|min:1|max:36500'
        ]);

        if ($user->role === 'superadmin') {
             return response()->json(['message' => 'Không thể ban Super Admin.'], 403);
        }

        $duration = $validated['duration_days'] ?? 36500; // 9999 năm = vĩnh viễn
        $bannedUntil = Carbon::now()->addDays($duration);

        // 1. Cập nhật CSDL
        $user->update([
            'banned_until' => $bannedUntil
        ]);

        // 2. "Đá" (kick) user ra khỏi hệ thống bằng cách xóa token
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Người dùng đã bị ban.',
            'banned_until' => $bannedUntil->toDateTimeString()
        ]);
    }

    /**
     * Gỡ ban (mở khóa) cho một người dùng.
     */
    public function unban(User $user)
    {
        $user->update([
            'banned_until' => null
        ]);

        return response()->json([
            'message' => 'Người dùng đã được gỡ ban.'
        ]);
    }
}
