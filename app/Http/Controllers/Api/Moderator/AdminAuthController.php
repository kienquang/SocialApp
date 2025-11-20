<?php

namespace App\Http\Controllers\Api\Moderator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
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

        // KIỂM TRA QUYỀN NGAY LẬP TỨC
        // Nếu không phải Mod hoặc Admin -> ĐÁ VĂNG RA
        if (!in_array($user->role, ['admin', 'superadmin', 'moderator'])) {


            throw ValidationException::withMessages([
                'email' => 'Tài khoản này không có quyền truy cập quản trị.',
            ]);
        }

        // Nếu là Admin, trả về token như bình thường
        $token = $user->createToken('admin-token')->plainTextToken;

        return response()->json([
            'user' => new \App\Http\Resources\UserResource($user),
            'token' => $token
        ]);
    }
}
