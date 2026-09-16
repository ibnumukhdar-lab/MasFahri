<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function form()
    {
        return view('auth.login');
    }

    public function masuk(Request $request)
    {
        $kredensial = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($kredensial, $request->boolean('ingat'))) {
            return back()->withErrors(['email' => 'Email atau sandi tidak cocok.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('profil'));
    }

    public function keluar(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function profil()
    {
        return view('profil', ['user' => Auth::user()]);
    }
}
