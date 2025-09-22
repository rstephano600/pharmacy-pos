<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        // Check if user is authenticated
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Please login to access this page.');
        }

        // Check if user has any of the required roles
        if (!in_array($user->role, $roles)) {
            // If user is admin but not the required role, redirect to admin dashboard
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'You do not have permission to access that area.');
            }

            // For regular users, redirect to their dashboard
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access that area.');
        }

        return $next($request);
    }
}