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

    public $notificationId;
    public $senderId;
    public $postId;
    public $followerIds;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($notificationId, $senderId, $postId, $followerIds)
    {
        $this->notificationId = $notificationId;
        $this->senderId = $senderId;
        $this->postId = $postId;
        $this->followerIds = $followerIds;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(empty($this->followerIds)) {
            return;
        }

        foreach ($this->followerIds as $followerId) {
            UserNotification::create([
                'user_id' => $followerId,
                'notification_id' => $this->notificationId,
                'sender_id' => $this->senderId,
                'read_at' => null,
                'post_id' => $this->postId,
                'comment_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
