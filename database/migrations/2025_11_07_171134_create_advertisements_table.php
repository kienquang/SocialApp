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
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Tiêu đề (Title) (để Admin (Quản trị viên) quản lý)
            $table->string('image_url', 500); // URL (Đường dẫn) ảnh (image) (từ Cloudinary)
            $table->string('link_url', 500);  // Link (Liên kết) mà quảng cáo (ad) trỏ đến

            // Vị trí (Position) (ví dụ: 'sidebar_top', 'in_feed', 'header_banner')
            // ->index() (chỉ mục) để tăng tốc (speed up) truy vấn (query) 'WHERE position' (NƠI vị trí)
            $table->string('position', 100)->index();

            // Trạng thái (Status) (ví dụ: 'active' (hoạt động), 'inactive' (không hoạt động))
            // ->index() (chỉ mục) để tăng tốc (speed up) truy vấn (query) 'WHERE status' (NƠI trạng thái)
            $table->string('status', 50)->default('active')->index();

            $table->integer('display_order')->default(0); // Thứ tự hiển thị (display) (nếu có nhiều quảng cáo (ads) cùng vị trí (position))
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
        Schema::dropIfExists('advertisements');
    }
};
