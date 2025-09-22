<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isAdmin()) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access the admin area.');
        }

        return $next($request);
    }
}