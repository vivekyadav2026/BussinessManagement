<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\SubscriptionService;

class CheckPlanFeature
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, $featureCode): Response
    {
        $orgId = auth()->user()->organization_id;

        if (!$orgId) {
            abort(403, 'No organization linked.');
        }

        if (!SubscriptionService::hasFeature($orgId, $featureCode)) {
            abort(403, 'Your current plan does not include access to ' . $featureCode . '. Please upgrade your plan.');
        }

        return $next($request);
    }
}
