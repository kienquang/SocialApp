<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConversationChange implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $conversationId;
    public $senderId;
    public $receiverId;
    public $lastMessageId;
    public $lastMessageContent;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($conversationId, $senderId, $receiverId, $lastMessageId, $lastMessageContent)
    {
        $this->conversationId = $conversationId;
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
        $this->lastMessageId = $lastMessageId;
        $this->lastMessageContent = $lastMessageContent;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel("converation.change.{$this->receiverId}");
    }
    public function broadcastAs()
    {
        return 'ConversationChange';
    }
    public function broadcastWith()
    {
        return [
            'conversationId' => $this->conversationId,
            'senderId' => $this->senderId,
            'receiverId' => $this->receiverId,
            'lastMessageId' => $this->lastMessageId,
            'lastMessageContent' => $this->lastMessageContent,
        ];
    }
}
