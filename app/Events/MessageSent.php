<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $SenderId;
    public $SenderName;
    public $RecieverId;
    public $MessageText;
    public $MessageId;
    public $imageUrl;
    public $createAt;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($SenderId, $SenderName, $RecieverId, $MessageText, $MessageId, $imageUrl = null, $createAt)
    {
        $this->SenderId = $SenderId;
        $this->SenderName = $SenderName;
        $this->RecieverId = $RecieverId;
        $this->MessageText = $MessageText;
        $this->MessageId = $MessageId;
        $this->imageUrl = $imageUrl;
        $this->createAt = $createAt;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel("App.Models.User.{$this->RecieverId}");
    }
    public function broadcastAs()
    {
        return 'MessageSent';
    }
    public function broadcastWith()
    {
        return [
            'SenderId' => $this->SenderId,
            'SenderName' => $this->SenderName,
            'RecieverId' => $this->RecieverId,
            'MessageText' => $this->MessageText,
            'MessageId' => $this->MessageId,
            'imageUrl' => $this->imageUrl,
            'createAt' => $this->createAt,
        ];
    }
}
