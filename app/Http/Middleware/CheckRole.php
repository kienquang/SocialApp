<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Định nghĩa cấp bậc cho các vai trò.
     * Cấp bậc cao hơn sẽ bao gồm tất cả quyền của cấp bậc thấp hơn.
     */
    private $roleHierarchy = [
        'user' => 1,
        'moderator' => 2,
        'admin' => 3,
        'superadmin' => 4,
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role  // Vai trò TỐI THIỂU yêu cầu (ví dụ: 'moderator')
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Kiểm tra xem user đã đăng nhập chưa
        if (!Auth::check()) {
            return response()->json(['message' => 'Yêu cầu xác thực.'], 401);
        }

        // 2. Lấy vai trò của user hiện tại
        $userRole = $request->user()->role;

        // 3. Lấy cấp bậc của user và cấp bậc yêu cầu
        // (Sử dụng `?? 0` để xử lý nếu vai trò không tồn tại trong bản đồ)
        $userLevel = $this->roleHierarchy[$userRole] ?? 0;
        $requiredLevel = $this->roleHierarchy[$role] ?? 99; // 99 là số lớn, auto-fail

        // 4. So sánh cấp bậc
        // Đây là logic then chốt: "Cấp bậc của bạn có LỚN HƠN HOẶC BẰNG cấp bậc yêu cầu không?"
        if ($userLevel >= $requiredLevel) {
            // Nếu có, cho phép request đi tiếp
            return $next($request);
        }

        // Nếu không đủ quyền, trả về lỗi 403 (Forbidden)
        return response()->json(['message' => 'Bạn không có quyền thực hiện hành động này.'], 403);
    }
}
