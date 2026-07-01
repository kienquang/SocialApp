<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(Request $request, $id, $hash): JsonResponse
    {
    // 1. Tìm User theo ID trên URL
        $user = User::find($id);

        // 2. Nếu không tìm thấy User -> Lỗi
        if (! $user) {
            return response()->json(['message' => 'Người dùng không tồn tại.'], 404);
        }

        // 3. Kiểm tra Hash (Bảo mật phụ)
        // Hash trên URL phải khớp với sha1 của email trong database
        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Link xác thực không hợp lệ.'], 403);
        }

        // 4. Kiểm tra xem đã xác thực chưa
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email đã được xác minh trước đó.']);
        }

        // 5. Đánh dấu đã xác thực
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json(['message' => 'Email đã được xác minh thành công.']);
    }
}
