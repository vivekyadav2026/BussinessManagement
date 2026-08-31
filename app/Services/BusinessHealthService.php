<?php

namespace App\Services;

class BusinessHealthService
{
    /**
     * Calculates the Business Health Score from 0 to 100 based on 5 pillars (20 points each).
     */
    public static function calculateScore($orgId, $locationId = null, $sales = null, $inventory = null, $receivables = null, $customers = null)
    {
        $sales = $sales ?? AnalyticsService::getSalesAndProfitMetrics($orgId, $locationId);
        $inventory = $inventory ?? AnalyticsService::getInventoryMetrics($orgId, $locationId);
        $receivables = $receivables ?? AnalyticsService::getReceivablesMetrics($orgId, $locationId);
        $customers = $customers ?? AnalyticsService::getCustomerMetrics($orgId);

        $score = 0;
        $insights = [];

        // 1. Sales Growth (20 points)
        if ($sales['sales_growth'] >= 10) {
            $score += 20;
        } elseif ($sales['sales_growth'] > 0) {
            $score += 15;
            $insights[] = "Sales are growing, but slowly ({$sales['sales_growth']}%).";
        } elseif ($sales['sales_growth'] > -10) {
            $score += 10;
            $insights[] = "Sales have slightly declined compared to last month.";
        } else {
            $score += 0;
            $insights[] = "Critical drop in sales ({$sales['sales_growth']}%).";
        }

        // 2. Profitability (20 points)
        if ($sales['profit_growth'] >= 10) {
            $score += 20;
        } elseif ($sales['profit_growth'] > 0) {
            $score += 15;
        } elseif ($sales['profit_growth'] > -10) {
            $score += 10;
            $insights[] = "Profit margins are tightening.";
        } else {
            $score += 0;
            $insights[] = "Significant drop in monthly profit.";
        }

        // 3. Inventory Health (20 points)
        if ($inventory['total_items'] > 0) {
            $lowStockRatio = $inventory['low_stock_count'] / $inventory['total_items'];
            if ($lowStockRatio === 0) {
                $score += 20;
            } elseif ($lowStockRatio <= 0.1) {
                $score += 15;
            } elseif ($lowStockRatio <= 0.3) {
                $score += 10;
                $insights[] = "Several items are running low on stock.";
            } else {
                $score += 5;
                $insights[] = "Warning: More than 30% of inventory is low on stock.";
            }
        } else {
            // No inventory data, give neutral points so we don't punish service businesses
            $score += 20;
        }

        // 4. Receivables Health (20 points)
        if ($receivables['outstanding'] > 0) {
            $overdueRatio = $receivables['overdue'] / $receivables['outstanding'];
            if ($overdueRatio === 0) {
                $score += 20;
            } elseif ($overdueRatio <= 0.1) {
                $score += 15;
            } elseif ($overdueRatio <= 0.3) {
                $score += 10;
                $insights[] = "A growing portion of receivables is overdue.";
            } else {
                $score += 0;
                $insights[] = "High risk: Over 30% of outstanding balance is overdue!";
            }
        } else {
            $score += 20; // No outstanding money is good money
        }

        // 5. Customer Growth (20 points)
        if ($customers['growth'] >= 5) {
            $score += 20;
        } elseif ($customers['growth'] > 0) {
            $score += 15;
        } elseif ($customers['total'] > 0 && $customers['new_month'] == 0) {
            $score += 5;
            $insights[] = "No new customers acquired this month.";
        } else {
            $score += 10; // Neutral fallback
        }

        return [
            'score' => $score,
            'color' => self::getScoreColor($score),
            'insights' => $insights
        ];
    }

    private static function getScoreColor($score)
    {
        if ($score >= 80) return 'text-green-500';
        if ($score >= 50) return 'text-yellow-500';
        return 'text-red-500';
    }
}
