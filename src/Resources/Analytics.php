<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK\Resources;

/**
 * Analytics and reporting resource
 *
 * Access detailed analytics and generate reports
 */
class Analytics extends Resource
{
    protected string $basePath = 'partner/analytics';

    /**
     * Get overview analytics
     */
    public function overview(array $params = []): array
    {
        return $this->client->get($this->path('overview'), $params);
    }

    /**
     * Get revenue analytics
     */
    public function revenue(array $params = []): array
    {
        return $this->client->get($this->path('revenue'), $params);
    }

    /**
     * Get revenue by date range
     */
    public function revenueByDateRange(string $startDate, string $endDate, string $granularity = 'day'): array
    {
        return $this->client->get($this->path('revenue/range'), [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'granularity' => $granularity,
        ]);
    }

    /**
     * Get product analytics
     */
    public function products(array $params = []): array
    {
        return $this->client->get($this->path('products'), $params);
    }

    /**
     * Get analytics for a specific product
     */
    public function product(int $productId, array $params = []): array
    {
        return $this->client->get($this->path("products/{$productId}"), $params);
    }

    /**
     * Get user/customer analytics
     */
    public function users(array $params = []): array
    {
        return $this->client->get($this->path('users'), $params);
    }

    /**
     * Get user acquisition analytics
     */
    public function acquisition(array $params = []): array
    {
        return $this->client->get($this->path('acquisition'), $params);
    }

    /**
     * Get retention analytics
     */
    public function retention(array $params = []): array
    {
        return $this->client->get($this->path('retention'), $params);
    }

    /**
     * Get cohort analysis
     */
    public function cohorts(array $params = []): array
    {
        return $this->client->get($this->path('cohorts'), $params);
    }

    /**
     * Get engagement analytics
     */
    public function engagement(array $params = []): array
    {
        return $this->client->get($this->path('engagement'), $params);
    }

    /**
     * Get content engagement (lessons, videos, etc.)
     */
    public function contentEngagement(array $params = []): array
    {
        return $this->client->get($this->path('content-engagement'), $params);
    }

    /**
     * Get completion rates for courses/formations
     */
    public function completionRates(array $params = []): array
    {
        return $this->client->get($this->path('completion-rates'), $params);
    }

    /**
     * Get funnel analytics
     */
    public function funnel(string $funnelName, array $params = []): array
    {
        return $this->client->get($this->path("funnels/{$funnelName}"), $params);
    }

    /**
     * Get conversion analytics
     */
    public function conversions(array $params = []): array
    {
        return $this->client->get($this->path('conversions'), $params);
    }

    /**
     * Get traffic sources
     */
    public function trafficSources(array $params = []): array
    {
        return $this->client->get($this->path('traffic-sources'), $params);
    }

    /**
     * Get geographic distribution
     */
    public function geographic(array $params = []): array
    {
        return $this->client->get($this->path('geographic'), $params);
    }

    /**
     * Get device/platform analytics
     */
    public function devices(array $params = []): array
    {
        return $this->client->get($this->path('devices'), $params);
    }

    /**
     * Get real-time analytics
     */
    public function realtime(): array
    {
        return $this->client->get($this->path('realtime'));
    }

    /**
     * Get time-based analytics (peak hours, days)
     */
    public function timeAnalysis(array $params = []): array
    {
        return $this->client->get($this->path('time-analysis'), $params);
    }

    /**
     * Get refund analytics
     */
    public function refunds(array $params = []): array
    {
        return $this->client->get($this->path('refunds'), $params);
    }

    /**
     * Get dispute/chargeback analytics
     */
    public function disputes(array $params = []): array
    {
        return $this->client->get($this->path('disputes'), $params);
    }

    /**
     * Create a custom report
     */
    public function createReport(array $config): array
    {
        return $this->client->post($this->path('reports'), $config);
    }

    /**
     * Get a saved report
     */
    public function getReport(int $reportId): array
    {
        return $this->client->get($this->path("reports/{$reportId}"));
    }

    /**
     * List saved reports
     */
    public function reports(array $params = []): array
    {
        return $this->client->get($this->path('reports'), $params);
    }

    /**
     * Delete a saved report
     */
    public function deleteReport(int $reportId): array
    {
        return $this->client->delete($this->path("reports/{$reportId}"));
    }

    /**
     * Export analytics data
     */
    public function export(string $type, string $format = 'csv', array $params = []): array
    {
        return $this->client->get($this->path("export/{$type}"), array_merge($params, ['format' => $format]));
    }

    /**
     * Schedule recurring report
     */
    public function scheduleReport(array $config): array
    {
        return $this->client->post($this->path('reports/schedule'), $config);
    }

    /**
     * Get scheduled reports
     */
    public function scheduledReports(): array
    {
        return $this->client->get($this->path('reports/scheduled'));
    }

    /**
     * Delete scheduled report
     */
    public function deleteScheduledReport(int $scheduleId): array
    {
        return $this->client->delete($this->path("reports/scheduled/{$scheduleId}"));
    }

    /**
     * Compare two periods
     */
    public function compare(string $period1Start, string $period1End, string $period2Start, string $period2End, array $metrics = []): array
    {
        return $this->client->get($this->path('compare'), [
            'period1_start' => $period1Start,
            'period1_end' => $period1End,
            'period2_start' => $period2Start,
            'period2_end' => $period2End,
            'metrics' => $metrics,
        ]);
    }

    /**
     * Get benchmark data (compare with similar partners)
     */
    public function benchmarks(array $params = []): array
    {
        return $this->client->get($this->path('benchmarks'), $params);
    }

    /**
     * Get AI-powered insights
     */
    public function insights(): array
    {
        return $this->client->get($this->path('insights'));
    }

    /**
     * Get anomaly detection results
     */
    public function anomalies(array $params = []): array
    {
        return $this->client->get($this->path('anomalies'), $params);
    }

    /**
     * Get predictions (revenue, churn, etc.)
     */
    public function predictions(string $metric, array $params = []): array
    {
        return $this->client->get($this->path("predictions/{$metric}"), $params);
    }
}
