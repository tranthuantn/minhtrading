<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class StudentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if ($request->user()->isAdmin()) {
            if (Route::has('admin.dashboard')) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('dashboard');
        }

        if (! $request->user()->isStudent()) {
            abort(403, 'Bạn không có quyền truy cập vào khu vực học viên.');
        }

        return $next($request);
    }
}
