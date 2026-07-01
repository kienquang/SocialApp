<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // <--- BẮT BUỘC
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserTyping implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $senderId;
    public $senderName;
    public $receiverId;
    public $isTyping; // true (Focus) hoặc false (Blur)

    public function __construct($senderId, $senderName, $receiverId, $isTyping)
    {
        $this->senderId = $senderId;
        $this->senderName = $senderName;
        $this->receiverId = $receiverId;
        $this->isTyping = $isTyping;
    }

    /**
     * Bắn vào kênh Private của NGƯỜI NHẬN (giống hệt logic MessageSent)
     */
    public function broadcastOn()
    {
        // Kênh này chính là kênh mà User B đang nghe để nhận tin nhắn mới
        return new PrivateChannel('App.Models.User.' . $this->receiverId);
    }

    /**
     * Đặt tên sự kiện ngắn gọn để Frontend dễ nghe
     */
    public function broadcastAs()
    {
        return 'typing';
    }

    /**
     * Dữ liệu gửi xuống Frontend
     */
    public function broadcastWith()
    {
        return [
            'sender_id' => $this->senderId,
            'name' => $this->senderName,
            'typing' => $this->isTyping
        ];
    }
}
