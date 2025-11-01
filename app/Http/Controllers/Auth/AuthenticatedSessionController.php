<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        // $request->session()->regenerate();

        $user = $request->user();

        // 2. Kiểm tra xem user có bị ban không (Accessor trong Model User sẽ tự chạy)
        if ($user->is_banned) {

            // --- ĐÂY LÀ PHẦN SỬA LỖI ---
            $bannedUntilFormatted = 'không xác định'; // Giá trị dự phòng
            try {
                // Tự "parse" (phân tích) string (chuỗi) sang Carbon
                // vì $casts (ép kiểu) chưa chạy ở bước này.
                $bannedUntilDate = Carbon::parse($user->banned_until);
                $bannedUntilFormatted = $bannedUntilDate->format('d-m-Y H:i:s');
            } catch (\Exception $e) {
                // (Bỏ qua nếu parse (phân tích) lỗi, $bannedUntilFormatted
                // sẽ giữ giá trị "không xác định")
            }
            // --- KẾT THÚC SỬA LỖI ---

            $message = __('auth.banned', ['time' => $bannedUntilFormatted]);

            // Hủy tất cả token (tokens)
            $user->tokens()->delete();

            // Ném lỗi Validation (Xác thực) 422
            throw ValidationException::withMessages([
                'email' => $message,
            ]);
        }

        // 4. (OK) Tạo token và trả về
        return response()->json([
            'user' => new \App\Http\Resources\UserResource($user), // Trả về UserResource
            'token' => $user->createToken('api-token')->plainTextToken,
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): jsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
        'message' => 'Logged out successfully',
    ]);
    }
}
