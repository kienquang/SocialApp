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
    ];

    /**
     * Lấy user (tác giả) của bài viết.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Lấy tất cả bình luận của bài viết.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
