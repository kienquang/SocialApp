<?php

namespace App\Http\Controllers\Realtime;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;
use App\Events\MessageSent;
use App\Models\Conversation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * Lấy danh sách user có thể chat (ngoại trừ bản thân)
     */
    public function conversationList() {
    $userId = auth()->id();
    //$userId = 1;
    $conversations = Conversation::with(['userOne', 'userTwo', 'lastMessage'])
        ->where('user_one_id', $userId)
        ->orWhere('user_two_id', $userId)
        ->orderByDesc('last_message_id')
        ->get();

    $conversationList = $conversations->map(function($conv) use ($userId) {
        $otherUser = $conv->user_one_id == $userId ? $conv->userTwo : $conv->userOne;
        return [
            'conversation_id' => $conv->id,
            'user' => [
                'id' => $otherUser->id,
                'name' => $otherUser->name,
                'avatar' => $otherUser->avatar
            ],
            'last_message_id' => $conv->last_message_id ? $conv->last_message_id : null,
        ];
    });

    return response()->json($conversationList);
    }

    /**
     * Lấy lịch sử tin nhắn giữa user hiện tại và 1 người cụ thể
     */
    public function fetchMessages($receiverId)
    {
        $userId = auth()->id();
        //$userId = 1;
        //dd($userId);
        $messages = Message::where(function ($query) use ($userId, $receiverId) {
                            $query->where('sender_id', $userId)
                                  ->where('receiver_id', $receiverId);
                        })
                        ->orWhere(function ($query) use ($userId, $receiverId) {
                            $query->where('sender_id', $receiverId)
                                  ->where('receiver_id', $userId);
                        })
                        ->orderBy('created_at', 'asc')
                        ->get();

        DB::table('conversations')
            ->where(function ($query) use ($userId, $receiverId) {
                $query->where('user_one_id', $userId)
                      ->where('user_two_id', $receiverId);
            })
            ->orWhere(function ($query) use ($userId, $receiverId) {
                $query->where('user_one_id', $receiverId)
                      ->where('user_two_id', $userId);
            })
            ->update(['last_read_message_id' => $messages->last()->id]);

        return response()->json($messages);
    }

    /**
     * Gửi tin nhắn (văn bản hoặc ảnh)
     */
    public function sendMessage(Request $request)
    {  
    try {
        $request->validate([
            'receiver_id' => 'required|integer|exists:users,id',
            'content' => 'string',
            'image_url' => 'nullable|string',
        ]);
        //dd(Message::class);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'content' => $request->input('content'),
            'image_url' => $request->input('image_url'),
            'created_at' => now(),
        ]);
        
        $this->updateOrCreateConversation(
            $request->receiver_id,
            $message->id
        );

        broadcast(new MessageSent(
            1,
            $request->receiver_id,
            $request->input('content'),
            $request->input('image_url')
        ));

        return response()->json($message);
     } catch (\Throwable $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ], 500);
     }
    }

    public function updateOrCreateConversation($receiverId, $lastMessageId = null)
    {
    $senderId = auth()->id();
    //$senderId = 1;
    // Sắp xếp ID để tránh tạo trùng
    [$userOne, $userTwo] = [$senderId, $receiverId];
    if ($userOne > $userTwo) {
        [$userOne, $userTwo] = [$userTwo, $userOne];
    }

    // Tìm conversation hiện có
     $conversation = Conversation::firstOrCreate(
        [
            'user_one_id' => $userOne,
            'user_two_id' => $userTwo,
        ]
    );

    // Nếu có lastMessageId thì cập nhật
    if ($lastMessageId !== null && 
        Schema::hasColumn('conversations', 'last_message_id')) {
        $conversation->update([
            'last_message_id' => $lastMessageId,
            'updated_at' => now(),
        ]);
    }

    return $conversation;
    }
}
