<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FollowedNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $follow;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($follow)
    {
        $this->follow = $follow;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('notifications.' . $this->follow->followed_id);
    }
    public function broadcastWith()
    {
        return [
            'follower_id' => $this->follow->follower_id,
            'followed_id' => $this->follow->followed_id,
            'type' => 'follow',
            'created_at' => $this->follow->created_at->toDateTimeString(),
        ];
    }
}
