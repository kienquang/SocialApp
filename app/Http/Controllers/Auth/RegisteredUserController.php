<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): jsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // --- BẮT ĐẦU KIỂM TRA EMAIL ---
        $apiKey = env('ABSTRACT_API_KEY');
        $email = $request->email;

        if ($apiKey) {
            try {
                $response = Http::get("https://emailreputation.abstractapi.com/v1/?api_key={$apiKey}&email={$email}");
                $data = $response->json();

                // 1. Kiểm tra Tồn tại (Deliverability)
                // Cấu trúc mới: $data['email_deliverability']['status']
                $deliverabilityStatus = $data['email_deliverability']['status'] ?? 'UNKNOWN';

                // API trả về 'undeliverable' (chữ thường)
                if ($deliverabilityStatus === 'undeliverable') {
                    throw ValidationException::withMessages([
                        'email' => 'Địa chỉ email này không tồn tại hoặc không thể nhận thư.',
                    ]);
                }

                // 2. Kiểm tra Email rác (Disposable)
                // Cấu trúc mới: $data['email_quality']['is_disposable']
                $isDisposable = $data['email_quality']['is_disposable'] ?? false;

                if ($isDisposable === true) {
                     throw ValidationException::withMessages([
                        'email' => 'Vui lòng không sử dụng email dùng một lần (email rác).',
                    ]);
                }

                // --- KẾT THÚC SỬA LỖI ---

            } catch (\Exception $e) {
                // Nếu là lỗi Validation (do mình ném ra ở trên) thì dừng lại và báo lỗi cho user
                if ($e instanceof ValidationException) {
                    throw $e;
                }
                // Các lỗi khác (kết nối API...) thì log lại nhưng cho qua để không chặn user
                Log::error("Lỗi Abstract API: " . $e->getMessage());
            }
        }


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        // Auth::login($user);
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
        'user' => $user,
        'token' => $token,
          ]);
    }
}
