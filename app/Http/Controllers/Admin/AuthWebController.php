<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthWebController extends Controller
{
    public function showLogin()
    {
        if (request()->session()->get('admin_logged_in')) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $ok = $data['username'] === env('ADMIN_USERNAME', 'admin')
           && $data['password'] === env('ADMIN_PASSWORD', 'admin');

        if (! $ok) {
            return back()->withErrors(['username' => 'Неверный логин или пароль.'])->withInput();
        }

        $request->session()->regenerate();
        $request->session()->put('admin_logged_in', true);
        $request->session()->put('admin_username', $data['username']);

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['admin_logged_in', 'admin_username']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
