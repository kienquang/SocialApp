<?php

namespace App\Http\Controllers\Api\Moderator;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\ReportCommentResource;
use App\Http\Resources\ReportPostResource;
use App\Http\Resources\ReportUserResource;
use App\Models\Comment;
use App\Models\Post;
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
        $validated = $request->validate([
            'user' => 'sometimes|string|min:2|max:100',
            'limit'=> 'sometimes|integer'
        ]);

        $searchTerm = $validated['user']?? "";
        $limit = $validated['limit']?? 5;
        $reports = Report_post::with([
                            'reporter:id,name,avatar', // Tối ưu: Chỉ lấy 3 cột
                            'post' // Tải "bằng chứng" (Post)
                        ])
                        ->whereHas('reporter', function ($query) use ($searchTerm) {
                                   $query->where('name', 'LIKE', '%'.$searchTerm.'%');})
                        ->orderBy('created_at', 'asc')
                        ->paginate($limit);

        return ReportPostResource::collection($reports);
    }

    /**
     * Lấy danh sách các BÁO CÁO BÌNH LUẬN (Comment Reports)
     */
    public function getCommentReports(Request $request)
    {
        $validated = $request->validate([
            'user' => 'sometimes|string|min:2|max:100',
            'limit'=> 'sometimes|integer'
        ]);

        $searchTerm = $validated['user']?? "";
        $limit = $validated['limit']?? 5;
        $reports = Report_comment::with([
                            'reporter:id,name,avatar',
                            'comment' // Tải "bằng chứng" (Comment)
                        ])
                        ->whereHas('reporter', function ($query) use ($searchTerm) {
                                   $query->where('name', 'LIKE', '%'.$searchTerm.'%');})
                        ->orderBy('created_at', 'asc')
                        ->paginate($limit);

        return ReportCommentResource::collection($reports);
    }

    /**
     * Lấy danh sách các BÁO CÁO NGƯỜI DÙNG (User Reports)
     */
    public function getUserReports(Request $request)
    {
        $validated = $request->validate([
            'user' => 'sometimes|string|min:2|max:100',
            'limit'=> 'sometimes|integer'
        ]);
        $searchTerm = $validated['user']?? "";
        $limit = $validated['limit']?? 5;
        $reports = Report_user::with([
                            'reporter:id,name,avatar',
                            'reportedUser:id,name,avatar,role,banned_until' // Tải "đối tượng"
                        ])
                        ->whereHas('reporter', function ($query) use ($searchTerm) {
                                   $query->where('name', 'LIKE', '%'.$searchTerm.'%');})
                        ->orderBy('created_at', 'asc')
                        ->paginate($limit);

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
    // Lấy các bài viết đã bị gỡ bỏ
    public function getRemovedPosts(Request $request){
        $posts = Post::where('status', 'removed_by_mod')
                        ->with(['user', 'category'])
                        ->withCount('allComments as comments_count')
                        ->orderby('updated_at','desc')
                        ->paginate(29);
        return PostResource::collection($posts);
    }
    //Khôi phục các bài viết đã bị gỡ bỏ
    public function restorePost(Post $post){
        if($post-> status === 'published'){
            return response()->json(['messange'=>'Bài viết này đang hiển thị bình thường.'],422);
        }

        $post->update(['status'=>'published']);
        return response()->json([
            'message'=>'Bài viết này đã được khôi phục',
            'post' => new PostResource($post)
        ]);
    }

    //Lấy các comment đã bị gỡ bỏ
    public function getRemovedComments(Request $request){
        $comments = Comment::where('status', 'removed_by_mod')
                            ->with(['user', 'post'])
                            ->orderby('updated_at', 'desc')
                            ->paginate(20);
        return CommentResource::collection($comments);
    }

    //Khôi phục các comment đã bị gỡ bỏ
    public function restoreComment(Comment $comment){
        if($comment->status ==='published'){
            return response()->json(['message'=>'Bình luận này đang được hiển thị bình thường']);
        }

        $comment->update(['status'=>'published']);

        return response()->json([
            'message'=>'Bình luận đã được khôi phục thành công',
            'comment'=> new CommentResource($comment),
        ]);
    }
}
