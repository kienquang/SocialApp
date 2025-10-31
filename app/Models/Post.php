<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    /**
     * Các trường được phép gán hàng loạt.
     */
    protected $fillable = [
        'title',
        'content_html',
        'user_id',
        'category_id',
        'thumbnail_url',
        'status',
    ];

    /**
     * Lấy user (tác giả) của bài viết.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * HÀM 1: Lấy các bình luận GỐC (top-level).
     * Dùng cho hàm show() để hiển thị.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }

    /**
     * HÀM 2: Lấy TẤT CẢ bình luận (bao gồm cả replies).
     * Dùng cho withCount() để đếm.
     */
    public function allComments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Những người dùng đã vote bài viết này.
     * Thêm withPivot('vote') để lấy cả cột 'vote'.
     */
    public function voters()
    {
        return $this->belongsToMany(User::class, 'post_votes')
                    ->withPivot('vote')
                    ->withTimestamps();
    }
    // Dùng để TÍNH TOÁN (withSum, withCount)
    public function votes()
    {
        return $this->hasMany(PostVote::class);
    }

    /**
     * Lấy category (chuyên mục) của bài viết.
     */
    public function category() // <-- THÊM MỚI
    {
        return $this->belongsTo(Category::class);
    }
}
