<?php

namespace App\Models;

// Thêm các use statement cần thiết
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // <-- Rất quan trọng cho API

class User extends Authenticatable // implements MustVerifyEmail (nếu bạn cần xác thực email)
{
    // Sử dụng các Trait
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Các trường được phép gán hàng loạt (mass-assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',      // Dùng cho Socialite
        'facebook_id',    // Dùng cho Socialite
        'avatar',
        // 'role' KHÔNG nên có ở đây.
        // Đây là một biện pháp bảo mật để ngăn người dùng tự gán 'admin' khi đăng ký.
    ];

    /**
     * Các trường nên được ẩn khi trả về JSON.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Các trường nên được ép kiểu (cast).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        // 'password' => 'hashed', // Dùng cho L10/L11. Bỏ qua nếu bạn dùng L8/L9
    ];

    /*
    |--------------------------------------------------------------------------
    | Mối quan hệ (Relationships)
    |--------------------------------------------------------------------------
    */

    /**
     * Lấy tất cả bài viết của người dùng.
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Lấy tất cả bình luận của người dùng.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Lấy tất cả thông báo của người dùng.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Lấy các tin nhắn người dùng đã gửi.
     */
    public function messagesSent()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Lấy các tin nhắn người dùng đã nhận.
     */
    public function messagesReceived()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Lấy các tin nhắn người dùng đã đọc.
     */
    public function messageReads()
    {
        return $this->hasMany(Message_read::class, 'reader_id');
    }

    /**
     * Lấy các lượt "mention" người dùng này đã thực hiện.
     */
    public function mentionsMade()
    {
        return $this->hasMany(Mention::class, 'mentioner_user_id');
    }

    /**
     * Lấy các lượt "mention" người dùng này đã nhận.
     */
    public function mentionsReceived()
    {
        return $this->hasMany(Mention::class, 'mentioned_user_id');
    }

    /**
     * Lấy các báo cáo (về user) mà người dùng này đã tạo.
     */
    public function reportsMadeOnUsers()
    {
        return $this->hasMany(Report_user::class, 'reporter_id');
    }

    /**
     * Lấy các báo cáo (về user) nhắm vào người dùng này.
     */
    public function reportsReceived()
    {
        return $this->hasMany(Report_user::class, 'reported_user_id');
    }

    /**
     * Lấy các báo cáo (về post) mà người dùng này đã tạo.
     */
    public function reportsMadeOnPosts()
    {
        return $this->hasMany(Report_post::class, 'reporter_id');
    }

    /**
     * Lấy các báo cáo (về comment) mà người dùng này đã tạo.
     */
    public function reportsMadeOnComments()
    {
        return $this->hasMany(Report_comment::class, 'reporter_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Mối quan hệ Nhiều-Nhiều (Follows)
    |--------------------------------------------------------------------------
    */

    /**
     * Lấy danh sách những người mà user này "Theo dõi" (Following).
     * (Những 'followed_id' mà 'follower_id' là user này)
     */
    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followed_id')
                    ->withTimestamps(); // Lấy cả created_at của bảng follows
    }

    /**
     * Lấy danh sách những người "Theo dõi" user này (Followers).
     * (Những 'follower_id' mà 'followed_id' là user này)
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'followed_id', 'follower_id')
                    ->withTimestamps(); // Lấy cả created_at của bảng follows
    }

    /**
     * Các bài viết mà người này đã vote.
     * Thêm withPivot('vote') để lấy cả cột 'vote'.
     */
    public function votedPosts()
    {
        return $this->belongsToMany(Post::class, 'post_votes')
                    ->withPivot('vote')
                    ->withTimestamps();
    }
}

