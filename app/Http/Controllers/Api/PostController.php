<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mews\Purifier\Facades\Purifier;

class PostController extends Controller
{
    public function __construct()
    {
        // Yêu cầu xác thực cho các hành động tạo, sửa, xóa
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }
    /**
     * Lấy danh sách bài viết (GET /api/posts)
     * (Hàm này không thay đổi, nó vẫn load 'user' và 'comments_count')
     */
    public function index(Request $request)
    {
        // 1. Logic sắp xếp (sort)
        $sort = $request->query('sort', 'newest'); // Mặc định là 'newest'

        $query = Post::query();

        if ($sort === 'hot') {
            // Sắp xếp 'hot': Tính điểm dựa trên tổng vote (score)
            // Sắp xếp theo cột 'vote_score' (được tạo bởi withSum) giảm dần
            $query->withSum('votes as vote_score', 'vote')
                  ->orderBy('vote_score', 'desc');
        } else {
            // Sắp xếp 'newest' (mặc định)
            $query->orderBy('created_at', 'desc');
        }

        // 2. Tải các quan hệ cần thiết
        // withSum: Tính tổng cột 'vote' và lưu vào 'vote_score'
        // withCount: Đếm số lượng bình luận
        $query->with(['user'])
              ->withSum('votes as vote_score', 'vote')
              ->withCount('allComments as comments_count'); // Đổi tên 'allComments' thành 'comments_count'

        // 3. Tải trạng thái vote của user hiện tại (nếu đã đăng nhập)
        if (Auth::guard('sanctum')->check()) {
            /** @var \App\Models\User $user */
            $user = Auth::guard('sanctum')->user();
            // Tải (load) quan hệ 'voters' nhưng chỉ cho user hiện tại
            $query->with(['voters' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }]);
        }

        // 4. Phân trang
        $posts = $query->paginate(10); // withQueryString() giữ lại tham số ?sort=hot

        return PostResource::collection($posts);
    }

    /**
     * Tạo bài viết mới (POST /api/posts)
     * (Hàm này không thay đổi)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content_html' => 'required|string',
        ]);

        // Làm sạch HTML trước khi lưu
        $validated['content_html'] = Purifier::clean($validated['content_html']);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Gọi 'posts()' từ biến $user
        $post = $user->posts()->create($validated);

        // Tải 'user' và tính 'vote_score' (ban đầu là 0) và 'comments_count'
        $post->load('user');
        $post->loadSum('votes as vote_score', 'vote');
        $post->loadCount('allComments as comments_count');
        // $post->voters rỗng (vì là bài mới), nên user_vote sẽ là 0

        return (new PostResource($post))
                ->response()
                ->setStatusCode(201); // 201 Created
    }

    /**
     * Lấy chi tiết một bài viết (GET /api/posts/{post})
     * *** HÀM NÀY ĐƯỢC CẬP NHẬT ***
     */
    /**
     * Hiển thị một bài viết cụ thể.
     */
    public function show(Post $post)
    {
        // 1. Tải quan hệ cơ bản
        // Tải tác giả (user)
        // Tải các bình luận gốc (top-level) và đính kèm (nested) tác giả của chúng + số lượng replies
        $post->load([
            'user',
            'comments' => function ($query) {
                $query->with('user')
                      ->withCount('replies')
                      ->orderBy('created_at', 'desc');
            }
        ]);

        // 2. Tải điểm số và số bình luận
        // loadSum: Tính tổng 'vote' và lưu vào 'vote_score'
        // loadCount: Đếm tổng số bình luận (allComments)
        $post->loadSum('votes as vote_score', 'vote');
        $post->loadCount('allComments as comments_count');

        // 3. Tải trạng thái vote của user hiện tại (nếu đã đăng nhập)
        if (Auth::guard('sanctum')->check()) {
            /** @var \App\Models\User $user */
            $user = Auth::guard('sanctum')->user();
            $post->load(['voters' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }]);
        }

        return new PostResource($post);
    }

    /**
     * Cập nhật một bài viết trong CSDL.
     */
    public function update(Request $request, Post $post)
    {
        // 1. Kiểm tra quyền (Authorization)
        $this->authorize('update', $post); // Sử dụng PostPolicy

        // 2. Validate dữ liệu
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content_html' => 'required|string',
        ]);

        // 3. Làm sạch HTML
        $validated['content_html'] = Purifier::clean($validated['content_html']);

        // 4. Cập nhật
        $post->update($validated);

        // 5. Tải lại các quan hệ cần thiết để trả về
        $post->load('user');
        $post->loadSum('votes as vote_score', 'vote');
        $post->loadCount('allComments as comments_count');

        if (Auth::guard('sanctum')->check()) {
            /** @var \App\Models\User $user */
            $user = Auth::guard('sanctum')->user();
            $post->load(['voters' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }]);
        }

        return new PostResource($post);
    }

    /**
     * Xóa một bài viết khỏi CSDL.
     */
    public function destroy(Post $post)
    {
        // 1. Kiểm tra quyền (Authorization)
        $this->authorize('delete', $post); // Sử dụng PostPolicy

        // 2. Xóa
        $post->delete();

        // 3. Trả về response trống
        return response()->noContent(); // 204 No Content
    }
}
