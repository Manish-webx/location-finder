<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthMiddleware
{
    /**
     * Handle an incoming request.
     * Restricts access to the admin dashboard and administration APIs.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->get('admin_logged_in')) {
            if ($request->expectsJson() || $request->is('admin-super-login/api/*') || $request->is('admin/api/*')) {
                return response()->json([
                    'error' => 'Unauthorized. Admin credentials required.',
                ], 401);
            }

            return redirect()->route('admin.login')->with('warning', 'Admin authentication required. Please log in to access the dashboard.');
        }

        return $next($request);
    }
}
