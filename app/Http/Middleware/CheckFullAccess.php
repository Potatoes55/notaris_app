<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckFullAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $user = auth()->user();
        if (
            ! session('access_all_menu')
            // ||
            // now()->greaterThan(session('access_expires_at'))

        ) {
            // return redirect()->route('login');
            return redirect()->route('settings')->with('error', 'Anda tidak memiliki akses penuh. Silakan hubungi administrator untuk mendapatkan akses.');
        }

        return $next($request);
    }
}
