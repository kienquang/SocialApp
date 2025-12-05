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
use Illuminate\Support\Facades\Auth;

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
                            'post.user' // Tải "bằng chứng" (Post)
                        ])
<<<<<<< HEAD
                        ->whereHas('post', function ($postQuery) use ($searchTerm) {
                        // Lọc các bản ghi Report_post chỉ khi Post của nó thỏa mãn điều kiện
                        $postQuery->whereHas('user', function ($userQuery) use ($searchTerm) {
                            // Điều kiện: Tên của tác giả (User Model) chứa từ khóa
                            $userQuery->where('name', 'LIKE', '%'.$searchTerm.'%');
                        });
                        })
                        ->orderBy('created_at', 'asc')
                        ->paginate($limit);
=======
                        ->orderBy('created_at', 'desc')
                        ->paginate(20);
>>>>>>> 25697107e27a3727c59d988f7a60532d5454465e

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
                            'comment.user' // Tải "bằng chứng" (Comment)
                        ])
<<<<<<< HEAD
                        ->whereHas('comment', function ($postQuery) use ($searchTerm) {
                        // Lọc các bản ghi Report_post chỉ khi Post của nó thỏa mãn điều kiện
                        $postQuery->whereHas('user', function ($userQuery) use ($searchTerm) {
                            // Điều kiện: Tên của tác giả (User Model) chứa từ khóa
                            $userQuery->where('name', 'LIKE', '%'.$searchTerm.'%');
                        });
                        })
                        ->orderBy('created_at', 'asc')
                        ->paginate($limit);
=======
                        ->orderBy('created_at', 'desc')
                        ->paginate(20);
>>>>>>> 25697107e27a3727c59d988f7a60532d5454465e

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
<<<<<<< HEAD
                        ->whereHas('reportedUser', function ($query) use ($searchTerm) {
                                   $query->where('name', 'LIKE', '%'.$searchTerm.'%');})
                        ->orderBy('created_at', 'asc')
                        ->paginate($limit);
=======
                        ->orderBy('created_at', 'desc')
                        ->paginate(20);
>>>>>>> 25697107e27a3727c59d988f7a60532d5454465e

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
        $request->validate([
            'sort' => 'in:newest,hot', // Chỉ chấp nhận 2 giá trị
            'limit' => 'sometimes|integer|min:1|max:50' ,
            'category' => 'nullable|integer|exists:categories,id' ,// (MỚI) Lọc theo Category
            'q' => 'nullable|string|max:255', //  Tham số tìm kiếm
            'user_id' => 'nullable|integer|exists:users,id',
        ]);

        $sortType = $request->query('sort', 'newest'); // Mặc định là 'newest'
        $limit = $request->query('limit', 10);
        $categoryId = $request->query('category'); // (MỚI)
        $searchTerm = $request->input('q');
        $userId = $request->query('user_id', null);

        /** @var Builder $query */
        $query = Post::query();

        // (QUAN TRỌNG) Chỉ lấy các bài viết đã được 'published' (công khai)
        $query->where('status', 'removed_by_mod');

        // 1. Tải các quan hệ cần thiết
        $query->with(['user', 'category']);

        // 2. Tải các số đếm
        // Dùng 'allComments' để đếm TẤT CẢ bình luận
        $query->withCount('allComments as comments_count');
        $query->withSum('votes as vote_score', 'vote'); // Đã sửa (dùng 'votes')

        // 5. Lọc (Filter) (Search (Tìm kiếm), Category (Chuyên mục), VÀ User (Người dùng))
        if ($searchTerm) {
            $query->where(function ($subQuery) use ($searchTerm) {
                $likeTerm = '%' . $searchTerm . '%';
                $subQuery->where('title', 'LIKE', $likeTerm)
                         ->orWhere('content_html', 'LIKE', $likeTerm)
                         ->orWhereHas('user', function ($userQuery) use ($likeTerm) {
                             $userQuery->where('name', 'LIKE', $likeTerm);
                         });
            });
        }
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        if ($userId) { // <-- (MỚI)
            $query->where('user_id', $userId);
        }

        // 4. Sắp xếp
        if ($sortType === 'hot') {
            // Sắp xếp theo điểm vote (cao -> thấp)
            // Cân nhắc thêm 'created_at' để ưu tiên bài mới hơn
            $query->orderByDesc('vote_score');
            $query->orderByDesc('created_at'); // Nếu cùng điểm, bài mới hơn lên trước
        } else {
            // Sắp xếp theo mới nhất (mặc định)
            $query->orderByDesc('created_at');
        }

        // 8. (ĐÃ SỬA) Tải (load) vote (phiếu bầu) của user (người dùng) hiện tại
        // Thử (try) lấy user (người dùng) từ 'sanctum' guard (bộ bảo vệ 'sanctum') (nếu token (mã) được gửi)
        /** @var \App\Models\User|null $user */
        $user = Auth::guard('sanctum')->user();

        if ($user) {
            // Nếu tìm thấy user (người dùng) (đã đăng nhập), tải (load) vote (phiếu bầu) của họ
            $query->with(['votes' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }]);
        }

        // 6. Phân trang
        /** @var \Illuminate\Pagination\LengthAwarePaginator $paginator */
        $paginator = $query->paginate($limit);
        $posts = $paginator->withQueryString();
        //$posts = $query->paginate($limit)->withQueryString();

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
        $validated = $request->validate([
            'limit'=>'nullable|integer',
            'user'=> 'nullable|string'
        ]);
        $limit= $validated['limit']?? 5;
        $searchItem= $validated['user']?? "";
        $comments = Comment::where('status', 'removed_by_mod')
                            ->whereHas('user', function ($query) use ($searchItem) {
                                   $query->where('name', 'LIKE', '%'.$searchItem.'%');})
                            ->with(['user', 'post'])
                            ->orderby('updated_at', 'desc')
                            ->paginate($limit);
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
