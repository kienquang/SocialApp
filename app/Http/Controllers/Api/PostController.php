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
    /**
     * Lấy danh sách bài viết (GET /api/posts)
     * (Hàm này không thay đổi, nó vẫn load 'user' và 'comments_count')
     */
    public function index(Request $request)
    {
        $sort = $request->query('sort', 'new'); // Mặc định là 'new'

        $query = Post::with('user')      // Eager load thông tin tác giả
                     ->withCount('comments'); // Đếm số bình luận

        if ($sort === 'hot') {
            $query->orderBy('comments_count', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $posts = $query->paginate(10);

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

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // *** BƯỚC LÀM SẠCH HTML CHỐNG XSS ***
        // Clean(input, config)
        // 'default' là cấu hình trong file config/purifier.php
        $clean_html = Purifier::clean($validated['content_html'], 'default');

        $post = $user->posts()->create([
            'title' => $validated['title'],
            'content_html' => $clean_html, // <-- Lưu HTML đã được làm sạch
        ]);

        return (new PostResource($post->load('user')))
                ->response()
                ->setStatusCode(201);
    }

    /**
     * Lấy chi tiết một bài viết (GET /api/posts/{post})
     * *** HÀM NÀY ĐƯỢC CẬP NHẬT ***
     */
    public function show(Post $post)
    {
        // Tải thông tin tác giả của bài viết
        // VÀ tải các bình luận (nhưng chỉ bình luận gốc)
        $post->load([
            'user',
            'comments' => function ($query) {
                // 1. Chỉ lấy bình luận gốc (không phải trả lời)
                $query->whereNull('parent_id')
                      // 2. Lấy tác giả của mỗi bình luận gốc
                      ->with('user')
                      // 3. Đếm số lượng trả lời cho mỗi bình luận gốc
                      ->withCount('replies')
                      // 4. Sắp xếp mới nhất lên đầu
                      ->orderBy('created_at', 'desc');
            }
        ]);

        return new PostResource($post);
    }

    /**
     * Cập nhật bài viết (PUT /api/posts/{post})
     * (Hàm này không thay đổi)
     */
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content_html' => 'required|string',
        ]);

        // *** LÀM SẠCH HTML KHI CẬP NHẬT ***
        $clean_html = Purifier::clean($validated['content_html'], 'default');

        $post->update([
            'title' => $validated['title'],
            'content_html' => $clean_html, // <-- Lưu HTML đã được làm sạch
        ]);

        $post->load('user')->loadCount('comments');
        return new PostResource($post);
    }


    /**
     * Xóa bài viết (DELETE /api/posts/{post})
     * (Hàm này không thay đổi)
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->noContent();
    }
}
