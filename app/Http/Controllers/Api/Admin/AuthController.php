<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Simple login check. Returns success if username/password match .env.
     * The front-end then just keeps sending the same credentials
     * (Basic Auth or X-Admin-* headers) with each admin request.
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $ok = $data['username'] === env('ADMIN_USERNAME', 'admin')
           && $data['password'] === env('ADMIN_PASSWORD', 'admin');

        if (! $ok) {
            return response()->json(['message' => 'Неверный логин или пароль.'], 401);
        }

        return response()->json([
            'message'  => 'Успешный вход.',
            'username' => $data['username'],
        ]);
    }

    /** Confirms credentials are valid (passes through middleware). */
    public function me(Request $request)
    {
        return response()->json([
            'username' => env('ADMIN_USERNAME', 'admin'),
            'authenticated' => true,
        ]);
    }
}
