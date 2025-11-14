<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\PostVote;

class VoteNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $vote;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($vote)
    {
        $this->vote = $vote;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel("notifications.{$this->vote->author_id}"); // broadcast chung 1 lần
    }
    public function broadcastWith()
    {
        return [
            'author_id' => $this->vote->author_id,
            'post_id' => $this->vote->post_id,
            'user_id' => $this->vote->user_id,
            'vote_value' => $this->vote->vote,
            'type' => 'vote',
            'created_at' => $this->vote->created_at->toDateTimeString(),
        ];
    }
}
