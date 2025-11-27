<?php

namespace App\Http\Controllers\Api\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserRoleController extends Controller
{
    /**
     * Cập nhật vai trò (role) của một người dùng cụ thể.
     * Chỉ Superadmin mới có thể làm điều này.
     */
    public function updateRole(Request $request, User $user)
    {
        // $user được tự động tìm thấy từ {user} trên route (Route Model Binding)

        $validated = $request->validate([
            'role' => [
                'required',
                'string',
                 // Đảm bảo vai trò mới phải là một trong các vai trò hợp lệ
                Rule::in(['user', 'moderator', 'admin', 'superadmin']),
            ]
        ]);

        // Kiểm tra logic nghiệp vụ (ví dụ: không cho phép hạ cấp superadmin khác)
        if ($user->role === 'superadmin' && $request->user()->id !== $user->id) {
             return response()->json(['message' => 'Không thể thay đổi vai trò của Superadmin khác.'], 403);
        }

        // Cập nhật vai trò
        $user->role = $validated['role'];
        $user->save();

        return response()->json([
            'message' => 'Cập nhật vai trò thành công.',
            'user' => $user->fresh(), // Trả về thông tin user đã cập nhật
        ]);
    }

    public function searchAdmin(Request $request){
        $validated = $request->validate([
            'role'=> 'sometimes|string',
            'limit'=> 'sometimes|integer|min:1|max:20'
        ]);

        $searchTerm = $validated['role']?? "";
        $limit = $validated['limit'] ?? 10;

        $query = User::where('role', '!=', 'user')
                        ->where('role', 'LIKE', $searchTerm)
                        ->paginate($limit);

        return UserResource::collection($query);
    }
}
