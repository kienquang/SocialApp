<?php

namespace App\Http\Controllers\Realtime;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\UserTyping;

class TypingController extends Controller
{
    public function trigger(Request $request)
    {
        // Validate dữ liệu
        $request->validate([
            'receiver_id' => 'required|integer',
            'typing' => 'required|boolean' // True = Focus, False = Blur
        ]);

        $receiverId = $request->input('receiver_id');
        $isTyping = $request->input('typing');
        $sender = auth()->user();

        broadcast(new UserTyping(
            $sender->id,
            $sender->name,
            $receiverId,
            $isTyping
        ));

        // Trả về 204 No Content
        return response()->noContent();
    }
}
