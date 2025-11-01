<?php

use App\Http\Controllers\Api\Admin\AdminCategoryController;
use App\Http\Controllers\Api\Admin\UserManagementController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\FollowController;
use App\Http\Controllers\Api\ImageUploadController;
use App\Http\Controllers\Api\Moderator\ModerationController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\PostVoteController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\Superadmin\UserRoleController;
use App\Http\Controllers\Api\UserProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

require __DIR__.'/auth.php';

// --- Route Public (Ai cũng xem được) ---
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

// API MỚI: Lấy các phản hồi của 1 bình luận
Route::get('/comments/{comment}/replies', [CommentController::class, 'getReplies']);

// Lấy danh sách chuyên mục
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);

// Routes xem Hồ sơ (Profile) công khai
// (Dùng 'user' làm tên tham số cho Model Binding)
Route::get('/profiles/{user}', [ProfileController::class, 'show']);
Route::get('/profiles/{user}/followers', [ProfileController::class, 'getFollowers']);
Route::get('/profiles/{user}/following', [ProfileController::class, 'getFollowing']);

Route::middleware('auth:sanctum')->group(function () {
    // API của Post
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);

    //API CỦA COMMENT
    Route::post('/comments', [CommentController::class, 'store']);
    Route::patch('/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

    // *** API UPLOAD ẢNH  ***
    // Dùng cho trình soạn thảo văn bản
    Route::post('/image-upload', [ImageUploadController::class, 'upload'])->name('image.upload');

    // --- Module Vote  ---
    Route::post('/posts/{post}/upvote', [PostVoteController::class, 'upvote']);
    Route::post('/posts/{post}/downvote', [PostVoteController::class, 'downvote']);

    // ---  CẬP NHẬT AVATAR ---
    Route::post('/user/avatar', [UserProfileController::class, 'updateAvatar']);

    // --- MODULE FOLLOW ---
    Route::post('/users/{user}/follow', [FollowController::class, 'toggleFollow']);

    // Module 8: Báo cáo (Gửi báo cáo)

    Route::post('/posts/{post}/report', [ReportController::class, 'storePostReport'])
         ->name('posts.report');

    Route::post('/comments/{comment}/report', [ReportController::class, 'storeCommentReport'])
         ->name('comments.report');

    Route::post('/users/{user}/report', [ReportController::class, 'storeUserReport'])
         ->name('users.report');
});

Route::middleware(['auth:sanctum', 'role:moderator'])
    ->prefix('moderator') // Tiền tố /api/moderator
    ->name('moderator.')
    ->group(function () {
        // 2. Thay thế Controller
        Route::get('/reports/posts', [ModerationController::class, 'getPostReports']);
        Route::get('/reports/comments', [ModerationController::class, 'getCommentReports']);
        Route::get('/reports/users', [ModerationController::class, 'getUserReports']);

        // Xử lý/Xóa một báo cáo (sau khi đã hành động, ví dụ: gỡ bài)
        Route::delete('/reports/posts/{reportPost}', [ModerationController::class, 'resolvePostReport']);
        Route::delete('/reports/comments/{reportComment}', [ModerationController::class, 'resolveCommentReport']);
        Route::delete('/reports/users/{reportUser}', [ModerationController::class, 'resolveUserReport']);
});

Route::middleware(['auth:sanctum', 'role:admin'])
    ->prefix('admin') // Tiền tố /api/admin
    ->name('admin.')
    ->group(function () {
           // Quản lý chuyên mục (Thêm/Sửa/Xóa)
        // Dùng apiResource để tạo nhanh các route:
        // GET /admin/categories -> index
        // POST /admin/categories -> store
        // GET /admin/categories/{category} -> show
        // PUT/PATCH /admin/categories/{category} -> update
        // DELETE /admin/categories/{category} -> destroy
        Route::apiResource('categories', AdminCategoryController::class);

        // Quản lý User (Ban/Unban)
        Route::post('/users/{user}/ban', [UserManagementController::class, 'ban']);
        Route::post('/users/{user}/unban', [UserManagementController::class, 'unban']);

        // Xem Lịch sử Kiểm duyệt (Moderation History) của 1 User
        Route::get('/users/{user}/moderation-history', [UserManagementController::class, 'getModerationHistory']);
});

Route::middleware(['auth:sanctum', 'role:superadmin'])
    ->prefix('superadmin') // Tiền tố /api/superadmin
    ->name('superadmin.')
    ->group(function () {
        Route::patch('/users/{user}/role', [UserRoleController::class, 'updateRole'])
             ->name('users.updateRole');

});




