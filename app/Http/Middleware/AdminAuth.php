<?php

namespace App\Http\Middleware;

use App\Models\AdminSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken() ?: $request->header('X-Auth-Token');

        if (! $token) {
            return response()->json(['message' => 'Требуется авторизация.'], 401);
        }

        $session = AdminSession::with('user')->where('token', $token)->first();

        if (! $session) {
            return response()->json(['message' => 'Недействительный токен.'], 401);
        }

        if ($session->isExpired()) {
            $session->delete();
            return response()->json(['message' => 'Сессия истекла. Войдите заново.'], 401);
        }

        // Make current admin user & session available to controllers.
        $request->attributes->set('admin_user', $session->user);
        $request->attributes->set('admin_session', $session);

        return $next($request);
    }
}
