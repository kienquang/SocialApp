<?php

namespace App\Http\Controllers\Realtime;

use App\Events\PostCreated;
use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Jobs\DistributeNotification;
use App\Jobs\MarkAllNotificationsRead;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        // Lấy tất cả notifications của user đang login
        $user = $request->user(); // hoặc Auth::user()

        // Eager load sender để lấy name và avatar
        $notifications = UserNotification::with('sender:id,name,avatar')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20); // phân trang nếu muốn
        $unread = $notifications->whereNull('read_at')->take(99)->count();

        // Chuyển về array để thêm thuộc tính
        $data = $notifications->toArray();

        // Thêm thuộc tính unread_count
        $data['unread_count'] = $unread;

        return response()->json($data);
    }

    //đánh dâu tất cả đã đọc
    public function markAllRead()
    {
        //xếp job vào queue
        MarkAllNotificationsRead::dispatch(auth()->id())->onQueue('mark-read');

        return response()->json(['status' => 'queued']);
    }

}
