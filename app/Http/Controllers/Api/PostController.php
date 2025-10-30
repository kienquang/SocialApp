<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate; // Dùng cho Policy
use Mews\Purifier\Facades\Purifier; // Dùng để chống XSS
use Illuminate\Database\Eloquent\Builder; // Dùng để type-hint $query

class PostController extends Controller
{
    /**
     * Khởi tạo, đăng ký policy
     * (Chúng ta sẽ dùng middleware 'can' trong file routes
     * thay vì gọi $this->authorize() ở đây)
     */
    public function __construct()
    {
        // Áp dụng policy cho các hàm tương ứng
        // 'post' là tên param trong route
        $this->middleware('can:update,post')->only('update');
        $this->middleware('can:delete,post')->only('destroy');
    }


    /**
     * Lấy danh sách bài viết (có phân trang và sắp xếp).
     */
    public function index(Request $request)
    {
        $request->validate([
            'sort' => 'in:newest,hot', // Chỉ chấp nhận 2 giá trị
            'limit' => 'sometimes|integer|min:1|max:50' ,
            'category' => 'nullable|integer|exists:categories,id' // (MỚI) Lọc theo Category
        ]);

        $sortType = $request->query('sort', 'newest'); // Mặc định là 'newest'
        $limit = $request->query('limit', 10);
        $categoryId = $request->query('category'); // (MỚI)

        /** @var Builder $query */
        $query = Post::query();

        // 1. Tải các quan hệ cần thiết
        // (MỚI: Thêm 'category')
        $query->with(['user', 'category']);

        // 2. Tải các số đếm
        // Dùng 'allComments' (đã sửa) để đếm TẤT CẢ bình luận
        $query->withCount('allComments as comments_count');
        $query->withSum('votes as vote_score', 'vote'); // Đã sửa (dùng 'votes')

        // (MỚI) 3. Lọc theo Category nếu có
        if ($categoryId) {
            $query->where('category_id', $categoryId);
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

        // 5. Tải vote của user hiện tại (nếu đã đăng nhập)
        if (Auth::check()) {
            $query->with(['voters' => function ($query) {
                $query->where('user_id', Auth::id());
            }]);
        }

        // 6. Phân trang
        $posts = $query->paginate($limit);
        // (Chúng ta đã xóa withQueryString() để tương thích L7)

        return PostResource::collection($posts);
    }

    /**
     * Tạo bài viết mới.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content_html' => 'required|string',
            // --- CẬP NHẬT ---
            'category_id' => 'required|integer|exists:categories,id',
            'thumbnail_url' => 'nullable|url|max:500', // Phải là URL (từ Cloudinary)
        ]);

        // Làm sạch HTML (Chống XSS)
        $safeContent = Purifier::clean($validated['content_html']);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Tạo bài viết
        $post = $user->posts()->create([
            'title' => $validated['title'],
            'content_html' => $safeContent,
            'category_id' => $validated['category_id'], // <-- THÊM MỚI
            'thumbnail_url' => $validated['thumbnail_url'], // <-- THÊM MỚI
        ]);

        // Tải lại các quan hệ cần thiết để trả về JSON chuẩn
        // (MỚI: Thêm 'category')
        $post->load(['user', 'category']);

        // Tải các giá trị (count/sum) cho bài viết mới
        // (Bài mới nên giá trị đều là 0)
        $post->loadCount('allComments as comments_count');
        $post->loadSum('votes as vote_score', 'vote');

        // 'voters' sẽ là collection rỗng (vì chưa ai vote)
        $post->load('voters');

        return (new PostResource($post))
                ->response()
                ->setStatusCode(201); // 201 Created
    }

    /**
     * Hiển thị chi tiết một bài viết.
     */
    public function show(Request $request, Post $post)
    {
        // Tải các quan hệ chính
        // (MỚI: Thêm 'category')
        $post->load(['user', 'category']);

        // Tải các số đếm
        $post->loadCount('allComments as comments_count');
        $post->loadSum('votes as vote_score', 'vote');

        // Tải bình luận GỐC (phân trang)
        $post->load([
            'comments' => function ($query) {
                $query->with('user') // Tải tác giả của bình luận
                      ->withCount('replies as replies_count') // Đếm số phản hồi
                      ->orderBy('created_at', 'asc'); // Cũ nhất trước
            }
        ]);

        // Tải vote của user hiện tại (nếu đã đăng nhập)
        if (Auth::check()) {
            $post->load(['voters' => function ($query) {
                $query->where('user_id', Auth::id());
            }]);
        }

        return new PostResource($post);
    }

    /**
     * Cập nhật bài viết.
     */
    public function update(Request $request, Post $post)
    {
        // Policy đã được check bởi middleware

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content_html' => 'required|string',
            // --- CẬP NHẬT ---
            'category_id' => 'required|integer|exists:categories,id',
            'thumbnail_url' => 'nullable|url|max:500',
        ]);

        // Làm sạch HTML (Chống XSS)
        $safeContent = Purifier::clean($validated['content_html']);

        $post->update([
            'title' => $validated['title'],
            'content_html' => $safeContent,
            'category_id' => $validated['category_id'], // <-- THÊM MỚI
            'thumbnail_url' => $validated['thumbnail_url'], // <-- THÊM MỚI
        ]);

        // Tải lại các quan hệ để trả về JSON mới nhất
        $post->load(['user', 'category']);

        // Không cần tải lại count/sum/voters vì chúng không đổi khi update
        // Nhưng nếu muốn trả về JSON đầy đủ, ta có thể load lại
        $post->loadCount('allComments as comments_count');
        $post->loadSum('votes as vote_score', 'vote');
        if (Auth::check()) {
            $post->load(['voters' => fn($q) => $q->where('user_id', Auth::id())]);
        }

        return new PostResource($post);
    }

    /**
     * Xóa bài viết.
     */
    public function destroy(Post $post)
    {
        // Policy đã được check bởi middleware

        $post->delete();

        // 204 No Content: Báo thành công, không cần trả về nội dung
        return response()->json(null, 204);
    }
}
