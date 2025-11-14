<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\UserNotification;

class StoreUserPostNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $notificationData;
    public $followerIds;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($notificationData, $followerIds)
    {
        $this->notificationData = $notificationData;
        $this->followerIds = $followerIds;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->followerIds as $followerId) {
            UserNotification::create([
                'user_id' => $followerId,
                'sender_id' => $this->notificationData['user_id'],
                'notification_id' => $this->notificationData['id'],
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
