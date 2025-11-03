<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostNotificationSent implements ShouldBroadcast
{
    public $notification;
    public $sender_id; // ID người gửi

    public function __construct($notification)
    {
        $this->notification = $notification;
        $this->sender_id = $notification->sender_id;
    }

    public function broadcastOn()
    {
        return new Channel('notifications.all'); // broadcast chung 1 lần
    }
}

