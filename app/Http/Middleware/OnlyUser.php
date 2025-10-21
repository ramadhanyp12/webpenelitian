<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnlyUser
{
    public function handle(Request $request, Closure $next)
    {
        // pastikan sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // ijinkan cuma role 'user'
        if (Auth::user()->role !== 'user') {
            abort(403, 'Hanya user yang boleh mengakses halaman ini.');
        }

        return $next($request);
    }
}
