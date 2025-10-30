<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Dùng để tự động tạo slug

class Category extends Model
{
    use HasFactory;

    /**
     * Các trường được phép gán hàng loạt.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Mutator: Tự động được gọi khi gán giá trị cho 'name'.
     *
     * @param  string  $value
     * @return void
     */
    public function setNameAttribute($value)
    {
        // 1. Gán giá trị 'name' bình thường
        $this->attributes['name'] = $value;

        // 2. Logic tự động tạo slug
        // Nếu slug không được gửi lên (empty)
        if (empty($this->attributes['slug'])) {
            // Tự động tạo slug từ name
            $this->attributes['slug'] = Str::slug($value);
        }
    }

    /**
     * Lấy tất cả bài viết thuộc chuyên mục này.
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}

