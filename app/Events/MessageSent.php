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

    public $RecieverId;
    public $MessageText;
    public $SenderName;
    public $imageUrl;
    public $SenderId;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($RecieverId, $MessageText, $SenderName, $imageUrl = null)
    {
        $this->SenderId = auth()->id();
        $this->RecieverId = $RecieverId;
        $this->MessageText = $MessageText;
        $this->SenderName = auth()->user()->name;
        $this->imageUrl = $imageUrl;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // Sử dụng channel đã được đăng ký trong routes/channels.php
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
            'imageUrl' => $this->imageUrl,
        ];
    }
}
