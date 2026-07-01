<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        return response()->json([
            'header' => $request->header('Authorization'),
            'bearer' => $request->bearerToken(),
            'env' => env('API_TOKEN'),
        ]);

        $staticToken = env('API_TOKEN');

        if ($request->bearerToken() !== $staticToken) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        return $next($request);
    }
}
