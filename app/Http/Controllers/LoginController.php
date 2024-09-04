<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function  view() {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Alamat email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.exists' => 'Alamat email tidak ditemukan.',
            'password.required' => 'Password harus diisi.',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            ActivityLog::create([
                'user_id' => Auth::id(),
                'timestamp' => now(),
                'activity' => 'Login',
                'description' => 'Pengguna Masuk'
            ]);

            return redirect()->intended('/dashboard');
        } else {
            // Password salah
            return back()->withErrors([
                'password' => 'Password yang dimasukkan salah.',
            ])->onlyInput('email');
        }
    }

    public function logout()
    {

        ActivityLog::create([
            'user_id' => Auth::id(),
            'timestamp' => now(),
            'activity' => 'Logout',
            'description' => 'Pengguna Keluar'
        ]);
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/login');
    }
}
