<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Dead-simple admin auth: ONE login / ONE password from .env.
 * No tokens, no sessions, no DB. Credentials are checked on every request.
 *
 * Accepts ANY of these (use whichever is convenient on the front-end):
 *   1) HTTP Basic Auth:           Authorization: Basic base64(user:pass)
 *   2) Headers:                   X-Admin-Username / X-Admin-Password
 *   3) Query / body fields:       username & password
 */
class AdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $expectedUser = env('ADMIN_USERNAME', 'admin');
        $expectedPass = env('ADMIN_PASSWORD', 'admin');

        // 1) HTTP Basic Auth
        $user = $request->getUser();
        $pass = $request->getPassword();

        // 2) custom headers
        if ($user === null) {
            $user = $request->header('X-Admin-Username');
            $pass = $request->header('X-Admin-Password');
        }

        // 3) query / body
        if ($user === null) {
            $user = $request->input('username');
            $pass = $request->input('password');
        }

        if ($user === $expectedUser && $pass === $expectedPass) {
            return $next($request);
        }

        return response()->json(['message' => 'Неверный логин или пароль.'], 401);
    }
}
