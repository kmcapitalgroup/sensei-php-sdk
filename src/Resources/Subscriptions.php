<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK\Resources;

use Sensei\PartnerSDK\Support\PaginatedResponse;

/**
 * Subscription management resource
 *
 * Manage customer subscriptions to products
 */
class Subscriptions extends Resource
{
    protected string $basePath = 'v1/partners/subscriptions';

    /**
     * List all subscriptions
     */
    public function all(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path(), $params);
    }

    /**
     * Get a specific subscription
     */
    public function get(int $id): array
    {
        return $this->find($id);
    }

    /**
     * Create a subscription for a user
     *
     * @param array $data [
     *   'user_id' => int (required),
     *   'product_id' => int (required),
     *   'pricing_tier_id' => int (required),
     *   'payment_method' => string|null,
     *   'start_date' => string|null (ISO 8601),
     *   'trial_ends_at' => string|null (ISO 8601),
     * ]
     */
    public function create(array $data): array
    {
        return $this->store($data);
    }

    /**
     * Update a subscription
     */
    public function updateSubscription(int $id, array $data): array
    {
        return $this->update($id, $data);
    }

    /**
     * Cancel a subscription
     */
    public function cancel(int $id, bool $immediately = false): array
    {
        return $this->client->post($this->path("{$id}/cancel"), [
            'immediately' => $immediately,
        ]);
    }

    /**
     * Resume a cancelled subscription
     */
    public function resume(int $id): array
    {
        return $this->client->post($this->path("{$id}/resume"));
    }

    /**
     * Pause a subscription
     */
    public function pause(int $id, ?string $resumeAt = null): array
    {
        $data = $resumeAt ? ['resume_at' => $resumeAt] : [];
        return $this->client->post($this->path("{$id}/pause"), $data);
    }

    /**
     * Unpause a subscription
     */
    public function unpause(int $id): array
    {
        return $this->client->post($this->path("{$id}/unpause"));
    }

    /**
     * Change subscription plan
     */
    public function changePlan(int $id, int $newPricingTierId, bool $prorate = true): array
    {
        return $this->client->post($this->path("{$id}/change-plan"), [
            'pricing_tier_id' => $newPricingTierId,
            'prorate' => $prorate,
        ]);
    }

    /**
     * Apply a coupon to subscription
     */
    public function applyCoupon(int $id, string $couponCode): array
    {
        return $this->client->post($this->path("{$id}/coupon"), [
            'coupon_code' => $couponCode,
        ]);
    }

    /**
     * Remove coupon from subscription
     */
    public function removeCoupon(int $id): array
    {
        return $this->client->delete($this->path("{$id}/coupon"));
    }

    /**
     * Get subscription invoices
     */
    public function invoices(int $id, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("{$id}/invoices"), $params);
    }

    /**
     * Get upcoming invoice preview
     */
    public function upcomingInvoice(int $id): array
    {
        return $this->client->get($this->path("{$id}/upcoming-invoice"));
    }

    /**
     * Extend subscription (add time)
     */
    public function extend(int $id, int $days): array
    {
        return $this->client->post($this->path("{$id}/extend"), [
            'days' => $days,
        ]);
    }

    /**
     * Grant complimentary access
     */
    public function grantAccess(int $userId, int $productId, int $days, string $reason = ''): array
    {
        return $this->client->post($this->path('grant-access'), [
            'user_id' => $userId,
            'product_id' => $productId,
            'days' => $days,
            'reason' => $reason,
        ]);
    }

    /**
     * Revoke access
     */
    public function revokeAccess(int $id, string $reason = ''): array
    {
        return $this->client->post($this->path("{$id}/revoke"), [
            'reason' => $reason,
        ]);
    }

    /**
     * Get subscription history/timeline
     */
    public function history(int $id): array
    {
        return $this->client->get($this->path("{$id}/history"));
    }

    /**
     * Check if user has access to product
     */
    public function checkAccess(int $userId, int $productId): array
    {
        return $this->client->get($this->path('check-access'), [
            'user_id' => $userId,
            'product_id' => $productId,
        ]);
    }

    /**
     * Get subscription metrics
     */
    public function metrics(array $params = []): array
    {
        return $this->client->get($this->path('metrics'), $params);
    }

    /**
     * Get churn analysis
     */
    public function churnAnalysis(array $params = []): array
    {
        return $this->client->get($this->path('churn-analysis'), $params);
    }

    /**
     * Get subscriptions expiring soon
     */
    public function expiringSoon(int $days = 7): PaginatedResponse
    {
        return $this->paginate($this->path('expiring-soon'), ['days' => $days]);
    }

    /**
     * Retry failed payment
     */
    public function retryPayment(int $id): array
    {
        return $this->client->post($this->path("{$id}/retry-payment"));
    }

    /**
     * Update payment method
     */
    public function updatePaymentMethod(int $id, string $paymentMethodId): array
    {
        return $this->client->put($this->path("{$id}/payment-method"), [
            'payment_method_id' => $paymentMethodId,
        ]);
    }
}
