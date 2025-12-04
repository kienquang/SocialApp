<?php

namespace App\Http\Controllers\Api\Moderator;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReportCommentResource;
use App\Http\Resources\ReportPostResource;
use App\Http\Resources\ReportUserResource;
use App\Models\Report_comment;
use App\Models\Report_post;
use App\Models\Report_user;
use Illuminate\Http\Request;

// ĐỔI TÊN CLASS: Từ ReportController -> ModerationController
class ModerationController extends Controller
{
    /**
     * Lấy danh sách các BÁO CÁO BÀI VIẾT (Post Reports)
     */
    public function getPostReports(Request $request)
    {
        $reports = Report_post::with([
                            'reporter:id,name,avatar', // Tối ưu: Chỉ lấy 3 cột
                            'post' // Tải "bằng chứng" (Post)
                        ])
                        ->orderBy('created_at', 'desc')
                        ->paginate(20);

        return ReportPostResource::collection($reports);
    }

    /**
     * Lấy danh sách các BÁO CÁO BÌNH LUẬN (Comment Reports)
     */
    public function getCommentReports(Request $request)
    {
        $reports = Report_comment::with([
                            'reporter:id,name,avatar',
                            'comment' // Tải "bằng chứng" (Comment)
                        ])
                        ->orderBy('created_at', 'desc')
                        ->paginate(20);

        return ReportCommentResource::collection($reports);
    }

    /**
     * Lấy danh sách các BÁO CÁO NGƯỜI DÙNG (User Reports)
     */
    public function getUserReports(Request $request)
    {
        $reports = Report_user::with([
                            'reporter:id,name,avatar',
                            'reportedUser:id,name,avatar,role,banned_until' // Tải "đối tượng"
                        ])
                        ->orderBy('created_at', 'desc')
                        ->paginate(20);

        return ReportUserResource::collection($reports);
    }

    /**
     * "Giải quyết" (Xóa) một Báo cáo Bài viết
     * (Sau khi Mod đã xem và gỡ bài viết vi phạm, họ sẽ xóa báo cáo này)
     */
    public function resolvePostReport(Report_post $reportPost)
    {
        // $reportPost được tự động tìm thấy (route model binding)
        $reportPost->delete();

        return response()->json(['message' => 'Báo cáo đã được giải quyết.'], 200);
    }

    /**
     * "Giải quyết" (Xóa) một Báo cáo Bình luận
     */
    public function resolveCommentReport(Report_comment $reportComment)
    {
        $reportComment->delete();

        return response()->json(['message' => 'Báo cáo đã được giải quyết.'], 200);
    }

    /**
     * "Giải quyết" (Xóa) một Báo cáo Người dùng
     */
    public function resolveUserReport(Report_user $reportUser)
    {
        $reportUser->delete();

        return response()->json(['message' => 'Báo cáo đã được giải quyết.'], 200);
    }
}
