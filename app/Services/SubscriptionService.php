<?php

namespace App\Services;

use App\Models\Organization;
use App\Models\OrganizationSubscription;
use App\Models\SystemSetting;

class SubscriptionService
{
    public static function getActiveSubscription($orgId)
    {
        $org = Organization::find($orgId);
        return $org ? $org->activeSubscription : null;
    }

    public static function getActiveSubscriptions($orgId)
    {
        $org = Organization::find($orgId);
        if (!$org) return collect();

        $base = $org->activeSubscription;
        $addons = $org->activeAddons;

        $all = collect();
        if ($base) $all->push($base);
        foreach($addons as $addon) $all->push($addon);

        return $all;
    }

    /**
     * Get a specific feature's value from the active plan and addons.
     */
    public static function getFeatureValue($orgId, $featureCode, $default = 'Unlimited')
    {
        $subscriptions = self::getActiveSubscriptions($orgId);
        
        if ($subscriptions->isEmpty()) {
            return $default;
        }

        $isBoolean = false;
        $hasTrue = false;
        $totalNumeric = 0;
        $hasNumeric = false;
        $hasUnlimited = false;

        foreach ($subscriptions as $sub) {
            if (!$sub->plan) continue;
            
            $feature = $sub->plan->features->where('feature_code', $featureCode)->first();
            if ($feature && $feature->feature_value !== null && $feature->feature_value !== '') {
                $val = strtolower(trim((string)$feature->feature_value));
                
                if (in_array($val, ['true', 'yes', 'on'])) {
                    $isBoolean = true;
                    $hasTrue = true;
                } elseif (in_array($val, ['unlimited', 'infinite', 'all', '-1'])) {
                    $hasUnlimited = true;
                } elseif (is_numeric($val)) {
                    $hasNumeric = true;
                    $totalNumeric += (int)$val;
                }
            }
        }

        if ($hasUnlimited) return 'Unlimited';
        if ($isBoolean) return $hasTrue ? 'true' : 'false';
        if ($hasNumeric) return (string)$totalNumeric;
        
        return $default;
    }


    /**
     * Check if a feature is enabled (treated as boolean).
     */
    public static function hasFeature($orgId, $featureCode)
    {
        $val = self::getFeatureValue($orgId, $featureCode);
        if ($val === null) return false;
        return in_array(strtolower(trim((string)$val)), ['true', 'yes', 'on']);
    }


    /**
     * Check if the organization has reached a specific numerical limit.
     */
    public static function hasReachedLimit($orgId, $featureCode, $currentUsage)
    {
        $val = self::getFeatureValue($orgId, $featureCode);
        
        if ($val === null || trim((string)$val) === '') {
            return false; // No explicit limit defined on plan = unlimited usage allowed
        }

        if (in_array(strtolower(trim((string)$val)), ['unlimited', 'infinite', 'all', '-1'])) {
            return false;
        }

        return $currentUsage >= (int) $val;
    }


    /**
     * Check if the organization is on trial.
     */
    public static function isTrial($orgId)
    {
        $subscription = self::getActiveSubscription($orgId);
        if (!$subscription) return false;
        return $subscription->status === 'Trial';
    }

    /**
     * Check if the subscription or trial has expired.
     */
    public static function isExpired($orgId)
    {
        $org = Organization::find($orgId);
        if (!$org) return true;

        $subscription = self::getActiveSubscription($orgId);

        if ($subscription) {
            return false; // Active base plan found (date and status are valid)
        }
        
        $latestSub = OrganizationSubscription::where('organization_id', $orgId)
            ->whereHas('plan', function($q){ $q->where('type', 'base'); })
            ->latest('id')->first();
            
        if ($latestSub) {
            // If the latest sub has explicitly expired/cancelled status OR its end date has passed
            if (in_array($latestSub->status, ['Expired', 'Cancelled']) || 
                ($latestSub->ends_at && \Carbon\Carbon::parse($latestSub->ends_at)->isPast() && !\Carbon\Carbon::parse($latestSub->ends_at)->isToday())) {
                return true;
            }
        }

        // If no subscription record exists at all:
        $enableTrial = SystemSetting::get('enable_free_trial', '1');
        $trialDays = (int) SystemSetting::get('trial_days', 14);

        if ($enableTrial === '0' || $trialDays <= 0) {
            return true;
        }

        if (app()->environment('testing')) {
            return false;
        }

        if ($org->created_at && $org->created_at->diffInDays(now()) <= $trialDays) {
            return false;
        }

        return true;
    }

    /**
     * Get days remaining in trial or subscription.
     */
    public static function getDaysRemaining($orgId)
    {
        $subscription = self::getActiveSubscription($orgId);
        if (!$subscription || !$subscription->ends_at) return 0;

        if ($subscription->ends_at->isPast() && !$subscription->ends_at->isToday()) return 0;

        return (int) now()->startOfDay()->diffInDays($subscription->ends_at->startOfDay(), false);
    }
}
