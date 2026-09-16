<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use App\Models\Plan;
use App\Models\OrganizationSubscription;
use App\Models\Invoice;
use App\Models\GatewayPayment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $orgCount = Organization::count();
        $userCount = User::count();
        $activeOrgs = Organization::where('is_active', true)->count();
        $trialOrgs = OrganizationSubscription::where('status', 'Trial')
            ->where(function($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()->toDateString());
            })->count();
        $expiredOrgs = OrganizationSubscription::whereIn('status', ['Expired', 'Cancelled', 'Refunded'])->count();

        // Platform Revenue from Paid Subscriptions
        $totalRevenue = (float) GatewayPayment::where('entity_type', Plan::class)
            ->where('status', 'captured')->sum('amount');
            
        $thisMonthRevenue = (float) GatewayPayment::where('entity_type', Plan::class)
            ->where('status', 'captured')
            ->whereBetween('updated_at', [now()->startOfMonth(), now()])
            ->sum('amount');

        $lastMonthRevenue = (float) GatewayPayment::where('entity_type', Plan::class)
            ->where('status', 'captured')
            ->whereBetween('updated_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
            ->sum('amount');

        $revenueGrowth = $lastMonthRevenue > 0 
            ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1) 
            : ($thisMonthRevenue > 0 ? 100 : 0);

        // Dynamic 6-month Revenue trend chart
        $mrrLabels = [];
        $mrrData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $mrrLabels[] = $month->format('M Y');
            $monthlySum = (float) GatewayPayment::where('entity_type', Plan::class)
                ->where('status', 'captured')
                ->whereYear('updated_at', $month->year)
                ->whereMonth('updated_at', $month->month)
                ->sum('amount');
            $mrrData[] = $monthlySum;
        }

        // Subscriptions plan breakdown
        $plans = Plan::withCount('subscriptions')
            ->having('subscriptions_count', '>', 0)
            ->orderByDesc('subscriptions_count')
            ->get();
        $planLabels = [];
        $planData = [];
        foreach ($plans as $plan) {
            $planLabels[] = $plan->name;
            $planData[] = (int) $plan->subscriptions_count;
        }
        if (empty($planLabels) || array_sum($planData) == 0) {
            $planLabels = ['Free Trial', 'Active Plan', 'Expired'];
            $planData = [(int)$trialOrgs, max(0, (int)($activeOrgs - $trialOrgs)), (int)$expiredOrgs];
        }

        // Recent Organizations List
        $recentOrganizations = Organization::with('users')
            ->latest()
            ->take(6)
            ->get();

        return view('super-admin.dashboard', compact(
            'orgCount', 'userCount', 'activeOrgs', 'trialOrgs', 'expiredOrgs',
            'totalRevenue', 'thisMonthRevenue', 'revenueGrowth',
            'mrrLabels', 'mrrData', 'planLabels', 'planData',
            'recentOrganizations'
        ));
    }
}
