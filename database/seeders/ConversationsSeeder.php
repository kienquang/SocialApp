<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConversationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::table('conversations')->insert([
            ['id' => 1, 'user_one_id' => 1, 'user_two_id' => 2, 'last_message_id' => 2, 'last_read_message_id_one' => null, 'last_read_message_id_two' => null,'created_at' => '2025-09-05 09:15:00',
            'updated_at' => now(),]
        ]) ;
    }
}
