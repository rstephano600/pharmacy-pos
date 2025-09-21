<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPharmacyAssociation
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Skip check for roles that don't require pharmacy association
        if ($user->isAdmin() || $user->role === \App\Models\User::ROLE_USER) {
            return $next($request);
        }

        // For pharmacy staff roles, check if they have a pharmacy association
        if (!$user->hasPharmacy()) {
            return redirect()->route('pharmacy.association')
                ->with('warning', 'You need to be associated with a pharmacy to access this area.');
        }

        return $next($request);
    }
}