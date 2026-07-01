<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    use HasFactory;
    protected $table = 'user_notifications';
    protected $fillable = [
        'user_id',
        'notification_id',
        'sender_id',
        'read_at',
        'post_id',
        'comment_id',
        'type',
        'created_at',
        'updated_at',
    ];

     // Relation tới người gửi
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Relation tới user nhận
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relation tới post nếu có
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    // Relation tới comment nếu có
    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
}
