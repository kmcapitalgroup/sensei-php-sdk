<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK\Resources;

/**
 * Dashboard statistics resource
 *
 * Get overview statistics and KPIs
 */
class Dashboard extends Resource
{
    protected string $basePath = 'partner/dashboard';

    /**
     * Get dashboard overview
     */
    public function overview(): array
    {
        return $this->client->get($this->path());
    }

    /**
     * Get summary statistics
     */
    public function summary(array $params = []): array
    {
        return $this->client->get($this->path('summary'), $params);
    }

    /**
     * Get key performance indicators
     */
    public function kpis(array $params = []): array
    {
        return $this->client->get($this->path('kpis'), $params);
    }

    /**
     * Get revenue statistics
     */
    public function revenue(array $params = []): array
    {
        return $this->client->get($this->path('revenue'), $params);
    }

    /**
     * Get revenue breakdown by product
     */
    public function revenueByProduct(array $params = []): array
    {
        return $this->client->get($this->path('revenue/by-product'), $params);
    }

    /**
     * Get revenue breakdown by period
     */
    public function revenueByPeriod(string $period = 'month', array $params = []): array
    {
        return $this->client->get($this->path('revenue/by-period'), array_merge($params, ['period' => $period]));
    }

    /**
     * Get subscriber statistics
     */
    public function subscribers(array $params = []): array
    {
        return $this->client->get($this->path('subscribers'), $params);
    }

    /**
     * Get new subscribers over time
     */
    public function newSubscribers(array $params = []): array
    {
        return $this->client->get($this->path('subscribers/new'), $params);
    }

    /**
     * Get subscriber growth rate
     */
    public function subscriberGrowth(array $params = []): array
    {
        return $this->client->get($this->path('subscribers/growth'), $params);
    }

    /**
     * Get product performance
     */
    public function productPerformance(array $params = []): array
    {
        return $this->client->get($this->path('products/performance'), $params);
    }

    /**
     * Get top performing products
     */
    public function topProducts(int $limit = 10, array $params = []): array
    {
        return $this->client->get($this->path('products/top'), array_merge($params, ['limit' => $limit]));
    }

    /**
     * Get recent activity feed
     */
    public function activity(int $limit = 20): array
    {
        return $this->client->get($this->path('activity'), ['limit' => $limit]);
    }

    /**
     * Get notifications
     */
    public function notifications(array $params = []): array
    {
        return $this->client->get($this->path('notifications'), $params);
    }

    /**
     * Mark notification as read
     */
    public function markNotificationRead(int $notificationId): array
    {
        return $this->client->post($this->path("notifications/{$notificationId}/read"));
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead(): array
    {
        return $this->client->post($this->path('notifications/read-all'));
    }

    /**
     * Get conversion funnel statistics
     */
    public function conversionFunnel(array $params = []): array
    {
        return $this->client->get($this->path('conversion-funnel'), $params);
    }

    /**
     * Get churn rate
     */
    public function churnRate(array $params = []): array
    {
        return $this->client->get($this->path('churn-rate'), $params);
    }

    /**
     * Get lifetime value metrics
     */
    public function ltv(array $params = []): array
    {
        return $this->client->get($this->path('ltv'), $params);
    }

    /**
     * Get MRR (Monthly Recurring Revenue)
     */
    public function mrr(array $params = []): array
    {
        return $this->client->get($this->path('mrr'), $params);
    }

    /**
     * Get ARR (Annual Recurring Revenue)
     */
    public function arr(array $params = []): array
    {
        return $this->client->get($this->path('arr'), $params);
    }

    /**
     * Get engagement metrics
     */
    public function engagement(array $params = []): array
    {
        return $this->client->get($this->path('engagement'), $params);
    }

    /**
     * Get customer satisfaction metrics
     */
    public function satisfaction(array $params = []): array
    {
        return $this->client->get($this->path('satisfaction'), $params);
    }

    /**
     * Get goals and progress
     */
    public function goals(): array
    {
        return $this->client->get($this->path('goals'));
    }

    /**
     * Update a goal
     */
    public function updateGoal(int $goalId, array $data): array
    {
        return $this->client->put($this->path("goals/{$goalId}"), $data);
    }

    /**
     * Get comparison with previous period
     */
    public function comparison(string $period = 'month'): array
    {
        return $this->client->get($this->path('comparison'), ['period' => $period]);
    }

    /**
     * Export dashboard data
     */
    public function export(string $format = 'csv', array $params = []): array
    {
        return $this->client->get($this->path('export'), array_merge($params, ['format' => $format]));
    }
}
