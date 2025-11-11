<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class Comment extends Model
{
    use HasFactory;

    /**
     * Các trường được phép gán hàng loạt.
     */
    protected $fillable = [
        'user_id',
        'post_id',
        'parent_id',
        'content',
        'status',
    ];

    /**
     * Lấy user (tác giả) của bình luận.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Lấy các bình luận con (trả lời).
     */
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')
                        ->where('status', 'published');
    }


}
