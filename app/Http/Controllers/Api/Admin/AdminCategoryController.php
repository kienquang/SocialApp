<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Dùng để tạo slug

/**
 * Controller này dành cho ADMIN (Quản lý)
 * Xử lý Thêm, Sửa, Xóa (CRUD)
 * Được bảo vệ bằng middleware 'role:admin' trong file routes/api.php
 */
class AdminCategoryController extends Controller
{
    /**
     * Lấy danh sách chuyên mục (cho trang quản lý).
     */
    public function index()
    {
        // Admin có thể cần phân trang
        $categories = Category::latest()->paginate(20);
        return CategoryResource::collection($categories);
    }

    /**
     * Tạo chuyên mục mới.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories',
            'slug' => 'sometimes|string|max:150|unique:categories', // 'sometimes' = không bắt buộc
            'description' => 'nullable|string|max:255',
        ]);

        // Model `Category` (File 03) có mutator `setNameAttribute`
        // sẽ tự động tạo slug nếu 'slug' không được gửi lên.
        $category = Category::create($validated);

        return (new CategoryResource($category))
                ->response()
                // 201 Created
                ->setStatusCode(201);
    }

    /**
     * Hiển thị chi tiết 1 chuyên mục (cho admin).
     */
    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    /**
     * Cập nhật chuyên mục.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            // 'unique' phải bỏ qua ID của chính nó
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
            'slug' => 'sometimes|string|max:150|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string|max:255',
        ]);

        // Nếu user gửi slug rỗng, tạo slug từ name
        if (empty($validated['slug'])) {
            // Check nếu name cũng không đổi
            if ($validated['name'] !== $category->name) {
                 $validated['slug'] = Str::slug($validated['name']);
            }
        }

        $category->update($validated);

        return new CategoryResource($category);
    }

    /**
     * Xóa chuyên mục.
     * (Lưu ý: Bài viết sẽ bị set category_id = null
     * do logic 'onDelete('set null')' trong migration)
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json(['message' => 'Đã xóa chuyên mục thành công.'], 200);
    }
}
