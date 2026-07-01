<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Report_comment;
use App\Models\Report_post;
use App\Models\Report_user;

class DashboardController extends Controller
{
    /**
     * Lấy số liệu thống kê tổng quan (Overview Stats)
     */
    public function index()
    {
        // 1. Tổng số lượng cơ bản
        $totalUsers = User::count();
        $totalPosts = Post::count(); // Tổng tất cả bài (published + removed + ...)
        $totalComments = Comment::count();

        // Đếm số bài viết đang hiển thị (published)
        $publishedPostsCount = Post::where('status', 'published')->count();

        // Đếm số bài viết đang bị gỡ (removed_by_mod)
        $removedPostsCount = Post::where('status', 'removed_by_mod')->count();

        // Đếm số user đang bị ban (banned)
        $bannedUsersCount = User::whereNotNull('banned_until')
                                ->where('banned_until', '>', now())
                                ->count();

        // (MỚI) Đếm tổng số lượng báo cáo (Reports)
        $postReportsCount = Report_post::count();
        $commentReportsCount = Report_comment::count();
        $userReportsCount = Report_user::count();

        // 2. Thống kê tăng trưởng trong 30 ngày qua (Growth)
        $newUsersLast30Days = User::where('created_at', '>=', now()->subDays(30))->count();
        $newPostsLast30Days = Post::where('created_at', '>=', now()->subDays(30))->count();

        // 3. Top 5 Bài viết "Hot" nhất (Most Popular Posts)
        $topPosts = Post::with(['user:id,name,avatar', 'category:id,name'])
                        ->withSum('votes as vote_score', 'vote')
                        ->orderByDesc('vote_score')
                        ->limit(5)
                        ->get()
                        ->makeHidden(['content_html']);

        // 4. Top 5 User tích cực nhất (Most Active Users)
        $activeUsers = User::withCount('posts')
                           ->orderByDesc('posts_count')
                           ->limit(5)
                           ->get(['id', 'name', 'avatar', 'email']);

        // (ĐÃ XÓA) Phần biểu đồ chart_data

        return response()->json([
            'counts' => [
                'users' => $totalUsers,
                'posts_total' => $totalPosts,
                'posts_published' => $publishedPostsCount,
                'posts_removed' => $removedPostsCount,
                'comments' => $totalComments,
                'banned_users' => $bannedUsersCount,
                'reports_post' => $postReportsCount,
                'reports_comment' => $commentReportsCount,
                'reports_user' => $userReportsCount,       
            ],
            'growth' => [
                'new_users_30d' => $newUsersLast30Days,
                'new_posts_30d' => $newPostsLast30Days,
            ],
            'top_content' => [
                'posts' => $topPosts,
                'users' => $activeUsers,
            ],
        ]);
    }
}
