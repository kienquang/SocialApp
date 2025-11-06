<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model này đại diện cho bảng 'post_votes'.
 * Chúng ta dùng Model này để việc truy vấn
 * (đặc biệt là updateOrCreate) trở nên sạch sẽ hơn.
 */
class PostVote extends Model
{
    // Bỏ HasFactory nếu bạn không dùng seeder factory
    use HasFactory;

    /**
     * Tên bảng
     * @var string
     */
    protected $table = 'post_votes';

    /**
     * Các trường được phép gán hàng loạt (mass-assignable).
     * Rất quan trọng cho hàm updateOrCreate.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'post_id',
        'vote',
    ];

    /**
     * Model này sử dụng timestamps (created_at, updated_at)
     * (Giả sử migration 01_create_post_votes_table của bạn có $table->timestamps())
     * Nếu migration chỉ có created_at, hãy dùng:
     * public $timestamps = false;
     * const UPDATED_AT = null;
     */
}
