<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // if (!auth()->check() || !auth()->user()->isAdmin()) {
        //     abort(403, 'Bạn không có quyền truy cập.');
        // }
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}