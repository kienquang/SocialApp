<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('posts')->insert([
            [
                'id' => 1,
                'user_id' => 1,
                'title' => 'Khám phá miền Tây mùa nước nổi',
                'content_html' => '<h2>Miền Tây – Vùng đất hiền hòa</h2>
                <p>Miền Tây Nam Bộ là một trong những điểm đến hấp dẫn nhất Việt Nam. Nơi đây nổi tiếng với sông nước hữu tình, chợ nổi và những con người chân chất.</p>
                <img src="https://res.cloudinary.com/demo/image/upload/sample.jpg" alt="Miền Tây sông nước"/>
                <p>Khi đến mùa nước nổi, cánh đồng biến thành biển nước mênh mông. Người dân di chuyển bằng xuồng, ghe, tạo nên khung cảnh rất đặc trưng.</p>
                <img src="https://res.cloudinary.com/demo/image/upload/river.jpg" alt="Con sông miền Tây"/>
                <p>Bên cạnh đó, ẩm thực miền Tây cũng vô cùng phong phú với các món như lẩu mắm, cá linh, bông điên điển.</p>',
                'category_id' => 1, // Liên kết với Category 'Du lịch - Khám phá'
                'thumbnail_url' => 'https://res.cloudinary.com/dijvgjj4m/image/upload/v1761758066/laravel_posts/z17fq1clgwkilfbvvdbx.jpg',
                'created_at' => '2025-09-01 02:00:00',
                'updated_at' => '2025-09-01 02:00:00',
            ],
            [
                'id' => 2,
                'user_id' => 2,
                'title' => 'Lễ hội hoa anh đào Nhật Bản',
                'content_html' => '<h2>Ngắm hoa anh đào nở rộ</h2>
<p>Lễ hội hoa anh đào là dịp người dân và du khách cùng nhau thưởng ngoạn cảnh sắc tuyệt đẹp của mùa xuân.</p>
<img src="https://res.cloudinary.com/demo/image/upload/cherry_blossom.jpg" alt="Hoa anh đào"/>
<p>Bên cạnh việc ngắm hoa, người tham gia còn được trải nghiệm văn hóa truyền thống, ẩm thực và nghệ thuật Nhật Bản.</p>',
                'category_id' => 2, // Liên kết với Category 'Văn hóa - Xã hội'
                'thumbnail_url' => 'https://res.cloudinary.com/dijvgjj4m/image/upload/v1761758066/laravel_posts/z17fq1clgwkilfbvvdbx.jpg',
                'created_at' => '2025-09-05 08:30:00',
                'updated_at' => '2025-09-05 08:30:00',
            ],
            [
                'id' => 3,
                'user_id' => 1,
                'title' => 'Khám phá miền Tây mùa nước nổi',
                'content_html' => '<h2>Miền Tây – Vùng đất hiền hòa</h2>
                <p>Miền Tây Nam Bộ là một trong những điểm đến hấp dẫn nhất Việt Nam. Nơi đây nổi tiếng với sông nước hữu tình, chợ nổi và những con người chân chất.</p>
                <img src="https://res.cloudinary.com/demo/image/upload/sample.jpg" alt="Miền Tây sông nước"/>
                <p>Khi đến mùa nước nổi, cánh đồng biến thành biển nước mênh mông. Người dân di chuyển bằng xuồng, ghe, tạo nên khung cảnh rất đặc trưng.</p>
                <img src="https://res.cloudinary.com/demo/image/upload/river.jpg" alt="Con sông miền Tây"/>
                <p>Bên cạnh đó, ẩm thực miền Tây cũng vô cùng phong phú với các món như lẩu mắm, cá linh, bông điên điển.</p>',
                'category_id' => 1, // Liên kết với Category 'Du lịch - Khám phá'
                'thumbnail_url' => 'https://res.cloudinary.com/dijvgjj4m/image/upload/v1761758066/laravel_posts/z17fq1clgwkilfbvvdbx.jpg',
                'created_at' => '2025-09-01 02:00:00',
                'updated_at' => '2025-09-01 02:00:00',
            ],
            [
                'id' => 4,
                'user_id' => 1,
                'title' => 'Khám phá miền Tây mùa nước nổi',
                'content_html' => '<h2>Miền Tây – Vùng đất hiền hòa</h2>
                <p>Miền Tây Nam Bộ là một trong những điểm đến hấp dẫn nhất Việt Nam. Nơi đây nổi tiếng với sông nước hữu tình, chợ nổi và những con người chân chất.</p>
                <img src="https://res.cloudinary.com/demo/image/upload/sample.jpg" alt="Miền Tây sông nước"/>
                <p>Khi đến mùa nước nổi, cánh đồng biến thành biển nước mênh mông. Người dân di chuyển bằng xuồng, ghe, tạo nên khung cảnh rất đặc trưng.</p>
                <img src="https://res.cloudinary.com/demo/image/upload/river.jpg" alt="Con sông miền Tây"/>
                <p>Bên cạnh đó, ẩm thực miền Tây cũng vô cùng phong phú với các món như lẩu mắm, cá linh, bông điên điển.</p>',
                'category_id' => 1, // Liên kết với Category 'Du lịch - Khám phá'
                'thumbnail_url' => 'https://res.cloudinary.com/dijvgjj4m/image/upload/v1761758066/laravel_posts/z17fq1clgwkilfbvvdbx.jpg',
                'created_at' => '2025-09-01 02:00:00',
                'updated_at' => '2025-09-01 02:00:00',
            ],
            [
                'id' => 5,
                'user_id' => 2,
                'title' => 'Khám phá miền Tây mùa nước nổi',
                'content_html' => '<h2>Miền Tây – Vùng đất hiền hòa</h2>
                <p>Miền Tây Nam Bộ là một trong những điểm đến hấp dẫn nhất Việt Nam. Nơi đây nổi tiếng với sông nước hữu tình, chợ nổi và những con người chân chất.</p>
                <img src="https://res.cloudinary.com/demo/image/upload/sample.jpg" alt="Miền Tây sông nước"/>
                <p>Khi đến mùa nước nổi, cánh đồng biến thành biển nước mênh mông. Người dân di chuyển bằng xuồng, ghe, tạo nên khung cảnh rất đặc trưng.</p>
                <img src="https://res.cloudinary.com/demo/image/upload/river.jpg" alt="Con sông miền Tây"/>
                <p>Bên cạnh đó, ẩm thực miền Tây cũng vô cùng phong phú với các món như lẩu mắm, cá linh, bông điên điển.</p>',
                'category_id' => 1, // Liên kết với Category 'Du lịch - Khám phá'
                'thumbnail_url' => 'https://res.cloudinary.com/dijvgjj4m/image/upload/v1761758066/laravel_posts/z17fq1clgwkilfbvvdbx.jpg',
                'created_at' => '2025-09-01 02:00:00',
                'updated_at' => '2025-09-01 02:00:00',
            ],
            [
                'id' => 6,
                'user_id' => 3,
                'title' => 'Khám phá miền Tây mùa nước nổi',
                'content_html' => '<h2>Miền Tây – Vùng đất hiền hòa</h2>
                <p>Miền Tây Nam Bộ là một trong những điểm đến hấp dẫn nhất Việt Nam. Nơi đây nổi tiếng với sông nước hữu tình, chợ nổi và những con người chân chất.</p>
                <img src="https://res.cloudinary.com/demo/image/upload/sample.jpg" alt="Miền Tây sông nước"/>
                <p>Khi đến mùa nước nổi, cánh đồng biến thành biển nước mênh mông. Người dân di chuyển bằng xuồng, ghe, tạo nên khung cảnh rất đặc trưng.</p>
                <img src="https://res.cloudinary.com/demo/image/upload/river.jpg" alt="Con sông miền Tây"/>
                <p>Bên cạnh đó, ẩm thực miền Tây cũng vô cùng phong phú với các món như lẩu mắm, cá linh, bông điên điển.</p>',
                'category_id' => 1, // Liên kết với Category 'Du lịch - Khám phá'
                'thumbnail_url' => 'https://res.cloudinary.com/dijvgjj4m/image/upload/v1761758066/laravel_posts/z17fq1clgwkilfbvvdbx.jpg',
                'created_at' => '2025-09-01 02:00:00',
                'updated_at' => '2025-09-01 02:00:00',
            ],
        ]);
    }
}
