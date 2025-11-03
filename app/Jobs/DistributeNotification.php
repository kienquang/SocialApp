<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class DistributeNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $notificationId;
    protected $recipientIds;


    public function __construct($notificationId, array $recipientIds)
    {
        $this->notificationId = $notificationId;
        $this->recipientIds = $recipientIds;
    }

    public function handle()
    {
        $sender_id = auth()->id();

        $rows = collect($this->recipientIds)->map(fn($uid) => [
            'sender_id' => $sender_id,
            'user_id' => $uid,
            'notification_id' => $this->notificationId,
            'created_at' => now(),
        ])->toArray();

        DB::table('user_notifications')->insert($rows);
    }
}

