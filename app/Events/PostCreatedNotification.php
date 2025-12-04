<?php

namespace App\Events;

use App\Models\Post;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostCreatedNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $post_notification;
    public $followerId;
    public function __construct($post_notifcation,$followerId)
    {
        $this->post_notification = $post_notifcation;
        $this->followerId = $followerId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel("notifications.{$this->followerId}"); // broadcast chung 1 lần
    }
    public function broadcastWith()
    {
        return [
            'post_id' => $this->post_notification->post_id,
            'title' => $this->post_notification->title,
            'type' => 'post',
            'created_at' => $this->post_notification->created_at->toDateTimeString(),
            'sender' => [
                'id' => $this->post_notification->sender['id'],
                'name' => $this->post_notification->sender['name'],
                'avatar' => $this->post_notification->sender['avatar'],
            ]
        ];
    }
}
