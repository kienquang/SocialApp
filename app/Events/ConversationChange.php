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
    public $senderName;
    public $senderAvatar;
    public $receiverId;
    public $toId;
    public $lastMessageId;
    public $lastMessageContent;
    public $lastReadId;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($conversationId, $senderId, $senderName, $sendetAvatar, $receiverId, $toId, $lastMessageId, $lastMessageContent, $lastReadId)
    {
        $this->conversationId = $conversationId;
        $this->senderId = $senderId;
        $this->senderName = $senderName;
        $this->senderAvatar = $sendetAvatar;
        $this->receiverId = $receiverId;
        $this->toId = $toId;
        $this->lastMessageId = $lastMessageId;
        $this->lastMessageContent = $lastMessageContent;
        $this->lastReadId = $lastReadId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel("conversation.change.{$this->toId}");
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
            'SenderName' => $this->senderName,
            'SenderAvatar' => $this->senderAvatar,
            'receiverId' => $this->receiverId,
            'lastMessageId' => $this->lastMessageId,
            'lastMessageContent' => $this->lastMessageContent,
            'lastReadId' => $this->lastReadId,
        ];
    }
}
