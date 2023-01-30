<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;


class LoginController extends Controller
{
    public function index()
    {
        return view('Login');
    }

    public function login(Request $request)
    {
        $percobaan = 3;
        $maxMinutes = 5;
        $ip_client = $request->ip();

        if ($ip_client ===  '127.0.0.5') {
            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials)) {
                return redirect()->intended();
            } else {
                return redirect()->back()->with('loginError', 'Periksa kembali email dan password Anda');
            }
        } else {
            $key = 'percobaan_login_' . $ip_client;
            $failed_percobaan = Cache::get($key, 0);
            $failed_percobaan++;

            if ($failed_percobaan >= $percobaan) {
                $blocked_until = Cache::get($key . '_blocked_until', now());
                if ($blocked_until > now()) {
                    $seconds = $blocked_until->diffInSeconds(now());
                    return redirect()->back()->with('seconds', $seconds);
                } else {
                    Cache::forget($key);
                }
            }

            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials)) {
                Cache::forget($key . '_blocked_until');
                Cache::forget($key);
                return redirect()->intended();
            } else {
                $failed_percobaan++;
                Cache::put($key, $failed_percobaan, now()->addMinutes($maxMinutes));
                Cache::put($key . '_blocked_until', now()->addMinutes($maxMinutes), now()->addMinutes($maxMinutes));
                return redirect()->back()->with('loginError', 'Periksa kembali email dan password Anda.');
            }
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
