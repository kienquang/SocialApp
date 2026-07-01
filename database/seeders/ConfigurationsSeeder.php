<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigurationsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('configurations')->insert([
            [
                'key' => 'footer_links',
                'value' => '[]',
                'created_at' => '2025-12-05 21:07:46',
                'updated_at' => '2025-12-05 21:07:46',
            ],
            [
                'key' => 'footer_socials',
                'value' => '[]',
                'created_at' => '2025-12-05 21:07:46',
                'updated_at' => '2025-12-05 21:07:46',
            ],
            [
                'key' => 'site_logo',
                'value' => 'https://res.cloudinary.com/dijvgjj4m/image/upload/v1765005472/site_assets/rdiayhyqzskyrxzgwce7.png',
                'created_at' => '2025-12-05 21:16:49',
                'updated_at' => '2025-12-06 00:17:52',
            ],
            [
                'key' => 'site_background',
                'value' => 'https://res.cloudinary.com/dijvgjj4m/image/upload/v1765001227/background_assets/gdmnlpqszcmvhtn2a7io.jpg',
                'created_at' => '2025-12-05 21:18:40',
                'updated_at' => '2025-12-05 23:07:08',
            ],
        ]);
    }
}
