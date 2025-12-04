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
       Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();

            // Ai nhận thông báo
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Notification chung (bảng notifications)
            $table->foreignId('notification_id')
                  ->constrained('notifications')
                  ->onDelete('cascade');

            // Ai tạo notification
            $table->foreignId('sender_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Nếu là thông báo về post
            $table->foreignId('post_id')
                  ->nullable()
                  ->constrained('posts')
                  ->onDelete('cascade');

            // Nếu là thông báo về comment
            $table->foreignId('comment_id')
                  ->nullable()
                  ->constrained('comments')
                  ->onDelete('cascade');

            // Nếu là thông báo về reply comment
            $table->string('type')->notNullable();

            // Khi nào user đọc
            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            // Đảm bảo 1 notification chỉ tạo 1 lần cho mỗi user
            $table->unique(['user_id', 'notification_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_notifications');
    }
};
