<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $comment;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel("comment.{$this->comment->post_id}"); //
    }
    public function broadcastWith()
    {
        return [
            'id' => $this->comment->id,
            'post_id' => $this->comment->post_id,
            'parent_id' => $this->comment->parent_id,
            'content' => $this->comment->content,
            'created_at' => $this->comment->created_at->toDateTimeString(),
            'sender' => [
                'id' => $this->comment->sender['id'],
                'name' => $this->comment->sender['name'],
                'avatar' => $this->comment->sender['avatar'],
            ]
        ];
    }
}
