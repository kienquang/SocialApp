<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report_post extends Model
{
    use HasFactory;
<<<<<<< HEAD
=======
    protected $fillable = [
        'post_id',
        'reporter_id',
        'reason',
    ];

    /**
     * Lấy người gửi báo cáo.
     */
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /**
     * Lấy bài viết bị báo cáo.
     */
    public function post()
    {
        // Dùng withTrashed() để Admin vẫn xem được report
        // ngay cả khi bài viết đã bị "Xóa Mềm" (status='removed')
        // (Chúng ta sẽ dùng logic này ở Phần 2)
        return $this->belongsTo(Post::class, 'post_id');
    }
>>>>>>> origin/kienBranch
}
