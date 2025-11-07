<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class PasswordResetLinkController extends Controller
{
    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        // 1. Tải (Load) user (người dùng) từ CSDL (database) bằng email (thư điện tử)
        $user = User::where('email', $request->email)->first();

        // 2. Kiểm tra (Check) xem user (người dùng) có tồn tại (exist) VÀ có bị ban (khóa) không
        if ($user && $user->is_banned) { // <-- DÒNG KIỂM TRA (CHECK) "BAN" (KHÓA)

            // 3. Lấy (Get) ngày giờ (datetime) (nếu có)
            $bannedUntilFormatted = $user->banned_until
                ? $user->banned_until->format('d-m-Y H:i:s')
                : 'vô thời hạn'; // (Dự phòng (Fallback) nếu `banned_until` (ban đến khi) là null (rỗng))

            // 4. Lấy (Get) thông báo (message) lỗi (error) từ file lang (ngôn ngữ)
            $message = __('auth.banned', ['time' => $bannedUntilFormatted]);

            // 5. Ném (Throw) ra lỗi (error) 422 và DỪNG LẠI (STOP)
            throw ValidationException::withMessages([
                'email' => $message,
            ]);
        }
        //==============================================================
        // KẾT THÚC KIỂM TRA SỐ 1
        //==============================================================


        // (Nếu code (mã) chạy đến đây, có nghĩa là user (người dùng) KHÔNG bị ban (khóa))


        //==============================================================
        // (!!!) KIỂM TRA SỐ 2 (CHECK 2): KIỂM TRA "EMAIL TỒN TẠI"
        // (Đây là logic (logic) mặc định (default) của Laravel)
        //==============================================================

        // 6. Dòng này sẽ TỰ ĐỘNG làm 3 việc:
        //    a. Tìm (Find) user (người dùng) bằng 'email' (thư điện tử) (Kiểm tra (Check) Tồn tại (Exist))
        //    b. Tạo (Create) Token (Mã) Đặt lại (Reset)
        //    c. Gửi (Send) Email (Thư điện tử) (chứa link (liên kết) + token (mã))
        $status = Password::sendResetLink($request->only('email'));

        // 7. Kiểm tra (Check) $status (trạng thái) trả về (return) từ bước 6
        if ($status == Password::RESET_LINK_SENT) {
            // (THÀNH CÔNG: Email (Thư điện tử) tồn tại (exist) và đã gửi (sent))
            return response()->json(['message' => __($status)]);
        }

        // (THẤT BẠI: Email (Thư điện tử) KHÔNG tồn tại (exist))
        // (Ví dụ: $status (trạng thái) là 'passwords.user' (tức là "Không tìm thấy user (người dùng)..."))
        // 8. Ném (Throw) ra lỗi (error) 422 và DỪNG LẠI (STOP)
        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }
}
