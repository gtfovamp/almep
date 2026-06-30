<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Session-based guard for the web admin panel.
 * Logs in via .env credentials, stores a flag in the session.
 */
class AdminSessionAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->get('admin_logged_in')) {
            return redirect()->route('admin.login');
        }
        return $next($request);
    }
}
