<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class replyCommentNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $replyComment;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($replyComment)
    {
        $this->replyComment = $replyComment;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel("notifications.{$this->replyComment->reply_to_user_id}"); //
    }
    public function broadcastWith()
    {
        return [
            'reply_to_user_id' => $this->replyComment->reply_to_user_id,
            'post_id' => $this->replyComment->post_id,
            'author_id' => $this->replyComment->author_id,
            'type' => 'reply_comment',
            'created_at' => $this->replyComment->created_at->toDateTimeString(),
            'sender' => [
                'id' => $this->replyComment->sender['id'],
                'name' => $this->replyComment->sender['name'],
                'avatar' => $this->replyComment->sender['avatar'],
            ]
        ];
    }
}
