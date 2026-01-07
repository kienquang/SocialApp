<?php

namespace App\Http\Controllers\Realtime;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;
use App\Events\MessageSent;
use App\Events\ConversationChange;
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

    if (auth()->user()->email_verified_at == null) {
        return response()->json([
            'error' => 'Email chưa được xác thực. Vui lòng xác thực email để sử dụng chức năng chat.'
        ], 403);
    }

    // PHÂN TRANG
    $perPage = (int) request('per_page', 20);
    $page    = max(1, (int) request('page', 1));

    // Query cơ bản (giữ logic cũ)
    $baseQuery = Conversation::with(['userOne', 'userTwo', 'lastMessage'])
        ->where(function ($q) use ($userId) {
            $q->where('user_one_id', $userId)
              ->orWhere('user_two_id', $userId);
        });

    // Tổng số conversation
    $total = (clone $baseQuery)->count();

    // Lấy page hiện tại: conv mới nhất trước
    $conversations = $baseQuery
        ->orderByDesc('last_message_id')
        //->orderByDesc('conversations.id')
        ->skip(($page - 1) * $perPage)
        ->take($perPage)
        ->get();

    $conversationList = $conversations->map(function($conv) use ($userId) {
        $otherUser = $conv->user_one_id == $userId ? $conv->userTwo : $conv->userOne;

        return [
            'conversation_id' => $conv->id,
            'user' => [
                'id'     => $otherUser->id,
                'name'   => $otherUser->name,
                'avatar' => $otherUser->avatar
            ],
            'last_message' => $conv->lastMessage ? [
                'id'          => $conv->lastMessage ? $conv->lastMessage->id : null,
                'sender_id'   => $conv->lastMessage ? $conv->lastMessage->sender_id : null,
                'receiver_id' => $conv->lastMessage ? $conv->lastMessage->receiver_id : null,
                'content'     => $conv->lastMessage ? $conv->lastMessage->content : null,
                'image_url'   => $conv->lastMessage ? $conv->lastMessage->image_url : null,
                'created_at'  => $conv->lastMessage ? $conv->lastMessage->created_at : null,
            ] : null,
            'last_read_message_id' => $conv->user_one_id == $userId
                ? $conv->last_read_message_id_one
                : $conv->last_read_message_id_two,
        ];
    });

    $lastPage = $perPage > 0 ? (int) ceil($total / $perPage) : 1;

    return response()->json([
        'data' => $conversationList,
        'meta' => [
            'current_page' => $page,
            'per_page'     => $perPage,
            'total'        => $total,
            'last_page'    => $lastPage,
            'has_more'     => $page < $lastPage,
        ],
    ]);
}


    /**
     * Lấy lịch sử tin nhắn giữa user hiện tại và 1 người cụ thể
     */
    public function fetchMessages($receiverId)
{
    validator(['receiverId' => $receiverId], [
        'receiverId' => 'required|integer|exists:users,id|not_in:' . auth()->id(),
    ])->validate();

    $userId = auth()->id();
    //$userId = 1;

    if (auth()->user()->email_verified_at == null) {
        return response()->json([
            'error' => 'Email chưa được xác thực. Vui lòng xác thực email để sử dụng chức năng chat.'
        ], 403);
    }

    // PHÂN TRANG
    $perPage = (int) request('per_page', 20);
    $page    = max(1, (int) request('page', 1));

    // Query chung cho 2 chiều (giữ logic cũ)
    $baseQuery = Message::where(function ($query) use ($userId, $receiverId) {
                            $query->where('sender_id', $userId)
                                  ->where('receiver_id', $receiverId);
                        })
                        ->orWhere(function ($query) use ($userId, $receiverId) {
                            $query->where('sender_id', $receiverId)
                                  ->where('receiver_id', $userId);
                        });

    // Tổng số tin nhắn giữa 2 người
    $total = (clone $baseQuery)->count();

    // Tính last_page và tránh page vượt quá
    $lastPage = $perPage > 0 ? (int) ceil(max(1, $total) / $perPage) : 1;
    if ($page > $lastPage) {
        $page = $lastPage;
    }

    // Tính offset để page 1 là 20 tin NHẮN MỚI NHẤT
    $offset = max(0, $total - $page * $perPage);

    $messages = $baseQuery
        ->orderBy('created_at', 'asc') // vẫn giữ asc như logic cũ
       // ->orderBy('id', 'asc') // <--- THÊM DÒNG NÀY
        ->skip($offset)
        ->take($perPage)
        ->get();

    // Cập nhật trạng thái đã đọc trong cuộc trò chuyện
    $lastMessage = $messages->last();
    if ($lastMessage) {
        if ($userId < $receiverId) {
            $conversation = Conversation::where('user_one_id', $userId)
                                        ->where('user_two_id', $receiverId)
                                        ->first();
            if ($conversation) {
                $conversation->last_read_message_id_one = $lastMessage->id;
                $conversation->save();
            }
        } else {
            $conversation = Conversation::where('user_one_id', $receiverId)
                                        ->where('user_two_id', $userId)
                                        ->first();
            if ($conversation) {
                $conversation->last_read_message_id_two = $lastMessage->id;
                $conversation->save();
            }
        }
    }

    return response()->json([
        'data' => $messages,
        'meta' => [
            'current_page' => $page,
            'per_page'     => $perPage,
            'total'        => $total,
            'last_page'    => $lastPage,
            'has_more'     => $page < $lastPage,
        ],
    ]);
}

    /**
     * Gửi tin nhắn (văn bản hoặc ảnh)
     */
    public function sendMessage(Request $request)
    {
    try {
        $request->validate([
            'receiver_id' => 'required|integer|exists:users,id|not_in:' . auth()->id(),
            'content' => 'required_without:image_url|nullable|string',
            'image_url' => 'required_without:content|nullable|string',
        ]);
        //dd(Message::class);
        if(auth()->user()->email_verified_at == null) {
            return response()->json([
                'error' => 'Email chưa được xác thực. Vui lòng xác thực email để sử dụng chức năng chat.'
            ], 403);
        }
        $receiver = User::findOrFail($request->receiver_id);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'content' => $request->input('content'),
            'image_url' => $request->input('image_url'),
            'created_at' => now(),
        ]);

        $conversation=$this->updateOrCreateConversation(
            $request->receiver_id,
            $message->id
        );

        broadcast(new MessageSent(
            auth()->id(),
            auth()->user()->name,
            $message->receiver_id,
            $message->content,
            $message->id,
            $message->image_url,
            $message->created_at
        ));
        if(auth()->id() < $receiver->id) {
            $senderLastReadId = $conversation->last_read_message_id_one;
            $receiverLastReadId = $conversation->last_read_message_id_two;
        } else {
            $senderLastReadId = $conversation->last_read_message_id_two;
            $receiverLastReadId = $conversation->last_read_message_id_one;
        }

        broadcast(new ConversationChange(
            $conversation->id,
            auth()->id(),
            auth()->user()->name,
            auth()->user()->avatar,
            $receiver->id,
            $receiver->id,
            $message->id,
            $message->content,
            $receiverLastReadId
        ));

        broadcast(new ConversationChange(
            $conversation->id,
            auth()->id(),
            auth()->user()->name,
            auth()->user()->avatar,
            $receiver->id,
            auth()->id(),
            $message->id,
            $message->content,
            $senderLastReadId
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
     public function updateReadMessageForReceiver(Request $request){
        $request->validate([
            'senderId' => 'required|integer|exists:users,id|not_in:' . auth()->id(),
            'lastMessageId' => 'required|integer|exists:messages,id',
        ]);
        $this->updateOrCreateConversation($request->senderId, $request->lastMessageId);
     }
    public function updateOrCreateConversation($receiverId, $lastMessageId = null, )
    {
        $senderId = auth()->id();

        //sắp xếp ID, id nhỏ hơn luôn ở vị trí user_one_id
        [$userOne, $userTwo] = $senderId < $receiverId ? [$senderId, $receiverId] : [$receiverId, $senderId];

        $valuesToUpdate = [
            'updated_at' => now(), // Luôn cập nhật thời gian hoạt động mới nhất
        ];

        // Chỉ cập nhật last_message_id nếu có giá trị
        if (!empty($lastMessageId)) {
            $valuesToUpdate['last_message_id'] = $lastMessageId;
            if ($userOne == $senderId) {
                $valuesToUpdate['last_read_message_id_one'] = $lastMessageId;
            } else {
                $valuesToUpdate['last_read_message_id_two'] = $lastMessageId;
            }
        }

        return Conversation::updateOrCreate(
            [
                'user_one_id' => $userOne,
                'user_two_id' => $userTwo,
            ],
            $valuesToUpdate // Dữ liệu để cập nhật hoặc tạo mới
        );//vì mỗi conversation chỉ có 1 cặp user_one_id và user_two_id, nên không cần conversation_id để cập nhật
    }
}
