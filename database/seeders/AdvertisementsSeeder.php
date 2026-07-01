<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Advertisement; // Import (Nhập) Model (Mô hình)
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdvertisementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Xóa (Delete) dữ liệu cũ (old data) (nếu có)
        Advertisement::truncate();

        $now = Carbon::now();

        DB::table('advertisements')->insert([
            [
                'title' => 'Quảng cáo Sidebar (Thanh bên) (Active (Hoạt động))',
                'image_url' => 'https://res.cloudinary.com/demo/image/upload/q_auto:eco,f_auto/v1605205511/samples/ecommerce/accessories-bag.jpg',
                'link_url' => 'https://example-partner-1.com',
                'position' => 'sidebar_top', // Vị trí (Position) 1
                'status' => 'active', // Đang hoạt động (active)
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Quảng cáo In-Feed (Trong luồng) (Active (Hoạt động))',
                'image_url' => 'https://res.cloudinary.com/dijvgjj4m/image/upload/v1765004690/advertisements/xxg9fi83q8hd6p3ddmsy.jpg',
                'link_url' => 'https://example-partner-2.com',
                'position' => 'in_feed', // Vị trí (Position) 2
                'status' => 'active', // Đang hoạt động (active)
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Quảng cáo Cũ (Hết hạn (Expired)) (Inactive (Không hoạt động))',
                'image_url' => 'https://res.cloudinary.com/demo/image/upload/q_auto:eco,f_auto/v1605205498/samples/food/reca-mors.jpg',
                'link_url' => 'https://example-partner-3.com',
                'position' => 'sidebar_top', // Vị trí (Position) 1
                'status' => 'inactive', // ĐÃ HẾT HẠN (INACTIVE) (API (API) (Giao diện lập trình ứng dụng) public (công khai) sẽ lọc (filter) cái này ra)
                'created_at' => $now->subMonth(), // Đã tạo (create) 1 tháng (month) trước
                'updated_at' => $now->subMonth(),
            ],
        ]);
    }
}
