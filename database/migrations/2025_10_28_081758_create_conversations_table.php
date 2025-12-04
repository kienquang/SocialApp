<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();

            // Hai người tham gia trong cuộc trò chuyện 1-1
            $table->unsignedBigInteger('user_one_id');
            $table->unsignedBigInteger('user_two_id');

            // Tin nhắn cuối cùng (dùng để load nhanh danh sách chat)
            $table->unsignedBigInteger('last_message_id')->nullable();
            $table->unsignedBigInteger('last_read_message_id_one')->nullable();
            $table->unsignedBigInteger('last_read_message_id_two')->nullable();

            $table->timestamps();

            // Khóa ngoại
            $table->foreign('user_one_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_two_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('last_message_id')->references('id')->on('messages')->onDelete('set null');

            // Không cho trùng cặp user
            $table->unique(['user_one_id', 'user_two_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
