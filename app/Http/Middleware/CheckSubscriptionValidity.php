<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\SubscriptionService;

class CheckSubscriptionValidity
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Allow unauthenticated requests or Super Admin
        if (!$user || $user->hasRole('Super Admin')) {
            return $next($request);
        }

        $orgId = $user->organization_id;

        if (!$orgId) {
            return $next($request);
        }

        // Allow subscription page, logout, and profile routes
        if ($request->routeIs('organization.subscription.*') || $request->routeIs('logout') || $request->routeIs('profile.*')) {
            return $next($request);
        }

        // Check if subscription or trial is expired
        if (SubscriptionService::isExpired($orgId)) {
            return redirect()->route('organization.subscription.index')->with('error', 'Your free trial or subscription has expired. Please select a plan to unlock full access to your account.');
        }

        return $next($request);
    }
}
