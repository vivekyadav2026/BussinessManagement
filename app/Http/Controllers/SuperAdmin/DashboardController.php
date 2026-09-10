<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use App\Models\Plan;
use App\Models\OrganizationSubscription;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $orgCount = Organization::count();
        $userCount = User::count();
        $activeOrgs = Organization::where('is_active', true)->count();
        $trialOrgs = Organization::whereNotNull('trial_ends_at')->where('trial_ends_at', '>=', now())->count();
        $expiredOrgs = Organization::where('is_active', false)->count();

        // Platform Revenue from Paid Invoices
        $totalRevenue = (float) Invoice::where('status', 'Paid')->sum('amount_paid');
        $thisMonthRevenue = (float) Invoice::where('status', 'Paid')
            ->whereBetween('updated_at', [now()->startOfMonth(), now()])
            ->sum('amount_paid');

        $lastMonthRevenue = (float) Invoice::where('status', 'Paid')
            ->whereBetween('updated_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
            ->sum('amount_paid');

        $revenueGrowth = $lastMonthRevenue > 0 
            ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1) 
            : ($thisMonthRevenue > 0 ? 100 : 0);

        // Dynamic 6-month Revenue trend chart
        $mrrLabels = [];
        $mrrData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $mrrLabels[] = $month->format('M Y');
            $monthlySum = (float) Invoice::where('status', 'Paid')
                ->whereYear('updated_at', $month->year)
                ->whereMonth('updated_at', $month->month)
                ->sum('amount_paid');
            $mrrData[] = $monthlySum;
        }

        // Subscriptions plan breakdown
        $plans = Plan::withCount('subscriptions')->get();
        $planLabels = [];
        $planData = [];
        foreach ($plans as $plan) {
            $planLabels[] = $plan->name;
            $planData[] = $plan->subscriptions_count;
        }
        if (empty($planLabels) || array_sum($planData) == 0) {
            $planLabels = ['Free Trial', 'Active Plan', 'Expired'];
            $planData = [$trialOrgs, max(0, $activeOrgs - $trialOrgs), $expiredOrgs];
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
