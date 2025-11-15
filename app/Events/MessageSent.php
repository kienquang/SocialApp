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
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($RecieverId, $MessageText, $SenderName, $imageUrl = null)
    {
        $this->RecieverId = $RecieverId;
        $this->MessageText = $MessageText;
        $this->SenderName = $SenderName;
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
}
