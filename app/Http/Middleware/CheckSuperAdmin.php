<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || $user->role !== \App\Models\User::ROLE_SUPER_ADMIN) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access the super admin area.');
        }

        return $next($request);
    }
}