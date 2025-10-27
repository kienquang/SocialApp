<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MessageReadsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('message_reads')->insert([
            ['id' => 1, 'message_id' => 1, 'reader_id' => 2, 'read_at' => '2025-09-01 01:01:00'],
            ['id' => 2, 'message_id' => 2, 'reader_id' => 1, 'read_at' => '2025-09-01 01:06:00'],
        ]);
    }
}
