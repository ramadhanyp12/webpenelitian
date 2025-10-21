<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Pastikan user login dan role = admin
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request);
        }

        // Kalau bukan admin, kembalikan ke dashboard atau halaman lain
        return redirect('/dashboard')->with('error', 'Akses ditolak, hanya admin yang boleh.');
    }
}
