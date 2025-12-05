<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Tùy chỉnh link xác thực email để trỏ về Frontend
        VerifyEmail::createUrlUsing(function ($notifiable) {

            // 1. Lấy Frontend URL từ file .env (http://localhost:5173)
            $frontendUrl = config('app.frontend_url', 'http://localhost:5173');

            // 2. Tạo các tham số cần thiết cho chữ ký (Signature)
            // Lưu ý: Signature được tạo dựa trên URL của BACKEND để bảo mật.
            // Chúng ta cần lấy các tham số query (expires, signature) từ link gốc của backend

            $backendUrl = URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );

            // 3. Cắt lấy phần query string (?expires=...&signature=...)
            $queryString = parse_url($backendUrl, PHP_URL_QUERY);

            // 4. Ghép thành link Frontend
            // Ví dụ: http://localhost:5173/verify-email/18/hash?expires=...&signature=...
            return $frontendUrl . '/verify-email/' . $notifiable->getKey() . '/' . sha1($notifiable->getEmailForVerification()) . '?' . $queryString;
        });
    }
}
