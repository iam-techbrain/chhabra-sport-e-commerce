<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Sanctum\PersonalAccessToken;

class AdminAuthMiddleware
{
    /**
     * Handle an incoming request for sensitive admin API endpoints.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('sanctum')->user();

        // Fallback: check Bearer token manually if auth('sanctum')->user() returns null
        if (!$user && $request->bearerToken()) {
            $accessToken = PersonalAccessToken::findToken($request->bearerToken());
            if ($accessToken && $accessToken->tokenable) {
                $user = $accessToken->tokenable;
            }
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated access. Please log in to access this resource.'
            ], 401);
        }

        $userRole = strtolower(trim($user->role ?? ''));
        $userEmail = strtolower(trim($user->email ?? ''));

        // Verify Admin Privileges
        if ($userRole !== 'admin' && $userEmail !== 'admin@gmail.com' && !str_contains($userEmail, 'admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: Administrative privileges required.'
            ], 403);
        }

        // Attach authenticated user to request
        $request->setUserResolver(fn() => $user);

        return $next($request);
    }
}
