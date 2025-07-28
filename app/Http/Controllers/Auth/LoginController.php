<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'tendangnhap' => 'required',
            'matkhau' => 'required'
        ]);

        // Kiểm tra với MD5
        $user = User::where('tendangnhap', $credentials['tendangnhap'])
            ->where('matkhau', md5($credentials['matkhau']))
            ->first();

        if ($user) {
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'tendangnhap' => 'Thông tin đăng nhập không chính xác.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}