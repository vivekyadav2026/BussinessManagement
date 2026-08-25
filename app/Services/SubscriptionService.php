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
        return in_array($val, ['1', 'true', 'yes', 'on']);
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
}
