<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserNotificationSeeder extends Seeder
{
    public function run(): void
    {
        // Giả sử đã có user_id và notification_id trong DB
        $userIds = DB::table('users')->pluck('id')->take(10);
        $senderIds = DB::table('users')->pluck('id')->take(5);
        $notificationIds = DB::table('notifications')->pluck('id')->take(5);

        if ($userIds->isEmpty() || $notificationIds->isEmpty()) {
            $this->command->warn('⚠️ Chưa có dữ liệu trong bảng users hoặc notifications.');
            return;
        }

        $rows = [];

        foreach ($userIds as $uid) {
            foreach ($notificationIds as $nid) {
                $rows[] = [
                    'user_id' => $uid,
                    'notification_id' => $nid,
                    'sender_id' => $senderIds->random(),
                    'read_at' => rand(0, 1) ? now() : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('user_notifications')->insert($rows);

        $this->command->info('✅ Đã tạo dữ liệu mẫu cho bảng user_notifications.');
    }
}
