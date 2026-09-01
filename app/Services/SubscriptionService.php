<?php

namespace App\Services;

use App\Models\Organization;

class SubscriptionService
{
    /**
     * Get the active subscription for an organization.
     */
    public static function getActiveSubscription($orgId)
    {
        $org = Organization::with(['activeSubscription.plan.features'])->find($orgId);
        return $org ? $org->activeSubscription : null;
    }

    /**
     * Get a specific feature's value from the active plan.
     */
    public static function getFeatureValue($orgId, $featureCode)
    {
        $subscription = self::getActiveSubscription($orgId);
        
        if (!$subscription || !$subscription->plan) {
            return null;
        }

        $feature = $subscription->plan->features->where('feature_code', $featureCode)->first();
        return $feature ? $feature->feature_value : null;
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
        
        if ($val === null) {
            return true; // No limit defined = no access
        }

        if (strtolower($val) === 'unlimited' || strtolower($val) === 'infinite') {
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

        $subscription = $org->activeSubscription;
        if (!$subscription) return true;

        if (in_array($subscription->status, ['Expired', 'Cancelled'])) {
            return true;
        }

        if ($subscription->ends_at && $subscription->ends_at->isPast() && !$subscription->ends_at->isToday()) {
            return true;
        }

        return false;
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
