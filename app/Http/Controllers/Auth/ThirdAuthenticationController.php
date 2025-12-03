<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ThirdAuthenticationController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
        ->stateless()
        ->with(['prompt' => 'select_account'])
        ->redirectUrl(route('GoogleCallback'))
        ->redirect();
    }

     public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')
        ->stateless()
        ->user();//bỏ qua được, Intelephense đoán kiểu trả về là Laravel\Socialite\Contracts\Provider, tức là interface: nên sẽ báo lỗi stateless(), nhưng PHP không cần biết kiểu trả về tại thời điểm biên dịch như Java/C#. Miễn là object thực sự có method stateless() thì PHP vẫn chạy tốt.
        //dd($googleUser);
        $user = User::where('email', $googleUser->getEmail())->first();

        if (!$user) {
            $user = User::create([
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ]);
        }
        $token = $user->createToken('google-auth')->plainTextToken;
        return view('callback', [
            'token' => $token,
            'user' => $user,
            'status' => 'success'
        ]);
    }

    public function redirectToGithub()
    {
        return Socialite::driver('github')
        ->stateless()
        ->with(['prompt' => 'select_account'])
        ->redirectUrl(route('GithubCallback'))
        ->redirect();
    }

    public function handleGithubCallback()
    {
        $gitHubUser = Socialite::driver('github')
        ->stateless()
        ->user();

        $user = User::where('email', $gitHubUser->getEmail())->first();

        if (!$user) {
            $user = User::create([
                'email' => $gitHubUser->getEmail(),
                'name' => $gitHubUser->getEmail(),
                'github_id' => $gitHubUser->getId(),
                'avatar' => $gitHubUser->getAvatar(),
            ]);
        }

        $token = $user->createToken('github-auth')->plainTextToken;
         return view('callback', [
            'token' => $token,
            'user' => $user,
            'status' => 'success'
        ]);
    }
}
