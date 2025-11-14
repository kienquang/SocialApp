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
use App\Events\PostCreatedNotification;
use Illuminate\Support\Facades\Log;
use App\Jobs\StoreUserPostNotification;
use App\Models\Notification;
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
        $query->where('status', 'published');

        // 1. Tải các quan hệ cần thiết
        // (MỚI: Thêm 'category')
        $query->with(['user', 'category']);

        // 2. Tải các số đếm
        // Dùng 'allComments' (đã sửa) để đếm TẤT CẢ bình luận
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
            'status'=>'published',
        ]);

        $post_notification = [
            'post_id' => $post->id,
            'title' => $post->title,
            'author_id' => $user->id,
            'created_at' => now(),
        ];

        event(new PostCreatedNotification((object)$post_notification));

        Notification::create([
            'sender_id' => $user->id,
            'type' => 'post',
            'post_id' => $post->id,
            'comment_id' => null,
            'created_at' => now(),
        ]);

        $followerIds = $user->followers()->pluck('users.id')->toArray();

        dispatch(new StoreUserPostNotification($post, $followerIds));

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
        if ($post->status !== 'published') {
             return response()->json(['message' => 'Bài viết không tồn tại.'], 404);
        }
        // Tải các quan hệ chính
        $post->load(['user', 'category']);

        // Tải các số đếm
        $post->loadCount('allComments as comments_count');
        $post->loadSum('votes as vote_score', 'vote');


        $user = Auth::guard('sanctum')->user();
        // Tải vote của user hiện tại (nếu đã đăng nhập)
        if ($user) {
            $post->load(['votes' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }]);
            // (LOGIC (LOGIC) MỚI) 6. Tính toán (Calculate) `is_following_author` (trạng thái theo dõi tác giả)
            $isFollowing = $user->following()->where('followed_id', $post->user_id)->exists();
            $post->is_following_author = $isFollowing; // Thêm (Add) thuộc tính "ảo" (virtual)
        } else {
            // Nếu là khách (guest), mặc định (default) là false (sai)
            $post->is_following_author = false;
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
        // 1. Kiểm tra quyền (Policy sẽ cho phép Tác giả HOẶC Mod)
        $this->authorize('delete', $post);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $newStatus = '';
        $message = '';

        // 2. Quyết định trạng thái (status) mới
        if ($user->role === 'moderator' || $user->role === 'admin' || $user->role === 'superadmin') {
            // Đây là hành động "Gỡ bài" (Kiểm duyệt)
            // Bài viết này sẽ được giữ lại làm "bằng chứng"
            $newStatus = 'removed_by_mod';
            $message = 'Bài viết đã được gỡ bỏ bởi Ban Quản Trị.';
        } else {
            // Đây là hành động "Xóa bài" (Tác giả)
            // Chúng ta cũng có thể dùng 'deleted_by_author'
            // 'removed' là đủ hiểu, miễn là chúng ta phân biệt được
            $newStatus = 'removed_by_author';
            $message = 'Bài viết đã được xóa bởi tác giả.';
        }

        // 3. Cập nhật status
        $post->update([
            'status' => $newStatus
        ]);

        return response()->json(['message' => $message], 200);
    }
}
