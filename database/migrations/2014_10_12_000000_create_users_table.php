<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Tương đương bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY
            $table->string('name', 100); // Đã đổi từ username
            $table->string('email', 150)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('user');
            $table->string('google_id')->nullable();
            $table->string('facebook_id')->nullable();
            // (CẬP NHẬT) Thêm (Add) URL (Đường dẫn) mặc định (default)
            $table->string('avatar', 500)
                  ->nullable()
                  ->default('https://res.cloudinary.com/dijvgjj4m/image/upload/v1761655167/user_avatars/default_avatar_n2x7tq.jpg');

            // (MỚI) Thêm (Add) cột (column) 'cover_photo_url' (đường dẫn ảnh bìa)
            $table->string('cover_photo_url', 500)
                  ->nullable()
                  ->default('https://res.cloudinary.com/dijvgjj4m/image/upload/v1761655167/user_covers/default_cover_gkf3a6.jpg');

            $table->timestamp('banned_until')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
