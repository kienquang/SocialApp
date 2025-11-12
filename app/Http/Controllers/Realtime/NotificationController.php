<?php

namespace App\Http\Controllers\Realtime;

use App\Events\PostCreated;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Jobs\DistributeNotification;
use App\Jobs\MarkAllNotificationsRead;

class NotificationController extends Controller
{
    public function sendPostNotification(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'data' => 'required|string',
            'recipients' => 'required|array',
            'recipients.*' => 'integer|exists:users,id'
        ]);

        // Tạo thông báo gốc
        $notification = Notification::create([
            'sender_id' => auth()->id(),
            'type' => $request->type,
            'data' => $request->data,
        ]);

        $recipientIds = $request->input('recipients'); // mảng user_id

        DistributeNotification::dispatch($notification->id, $recipientIds);

        // broadcast đến client qua Pusher
        broadcast(new PostCreated($notification));

        return response()->json(['status' => 'queued']);
    }

     public function index(Request $request)
    {
        $userId = auth()->id();
        //$userId = 1;

        $notifications = DB::table('user_notifications as un')
            ->join('notifications as n', 'un.notification_id', '=', 'n.id')
            ->where('un.user_id', $userId)
            ->select('n.id', 'n.sender_id', 'n.data', 'n.created_at', 'un.read_at')
            ->orderByDesc('n.created_at')
            ->paginate(20);

        return response()->json($notifications);
    }

    //đánh dâu tất cả đã đọc
    public function markAllRead()
    {
        //xếp job vào queue 
        MarkAllNotificationsRead::dispatch(auth()->id());

        return response()->json(['status' => 'queued']);
    }
    
}
