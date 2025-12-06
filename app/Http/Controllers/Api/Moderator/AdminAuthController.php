<?php

namespace App\Http\Controllers\Api\Moderator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;


class AdminAuthController extends Controller
{
    /**
     * Đăng nhập dành riêng cho Admin/Mod
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $user = $request->user();

        // Nếu không phải Mod/Admin/Superadmin -> từ chối
        if (!in_array($user->role, ['admin', 'superadmin', 'moderator'])) {
            throw ValidationException::withMessages([
                'email' => 'Tài khoản này không có quyền truy cập quản trị.',
            ]);
        }

        $token = $user->createToken('admin-token')->plainTextToken;

        // Lấy data từ UserResource như bình thường
        $userData = (new UserResource($user))->toArray($request);

        // CHỈ THÊM role KHI ĐĂNG NHẬP QUA CỔNG ADMIN
        // (ở đây chắc chắn user là admin/superadmin/mod rồi)
        $userData['role'] = $user->role;

        return response()->json([
            'user'  => $userData,
            'token' => $token,
        ]);
    }
}
