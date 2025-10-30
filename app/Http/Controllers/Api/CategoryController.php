<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

/**
 * Controller này dành cho PUBLIC (ai cũng xem được)
 * Chỉ xử lý việc lấy danh sách.
 */
class CategoryController extends Controller
{
    /**
     * Lấy danh sách tất cả chuyên mục.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        // Tương lai có thể thêm cache ở đây
        // Thêm withCount('posts') nếu bạn muốn đếm số bài viết
        $categories = Category::orderBy('name')->get();

        return CategoryResource::collection($categories);
    }

    /**
     * Lấy chi tiết một chuyên mục (thường không cần thiết
     * nếu chỉ dùng để lọc, nhưng cứ để đây).
     *
     * @param  \App\Models\Category  $category
     * @return \App\Http\Resources\CategoryResource
     */
    public function show(Category $category)
    {
        // Có thể load thêm bài viết nếu cần
        // $category->load('posts');
        return new CategoryResource($category);
    }
}
