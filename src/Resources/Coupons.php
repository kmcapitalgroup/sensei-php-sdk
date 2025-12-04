<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK\Resources;

use Sensei\PartnerSDK\Support\PaginatedResponse;

/**
 * Coupons resource
 *
 * Manage discount coupons and promotions
 */
class Coupons extends Resource
{
    protected string $basePath = 'partner/coupons';

    // === Coupons ===

    /**
     * List all coupons
     */
    public function all(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path(), $params);
    }

    /**
     * Get coupon
     */
    public function get(int $couponId): array
    {
        return $this->client->get($this->path($couponId));
    }

    /**
     * Get coupon by code
     */
    public function getByCode(string $code): array
    {
        return $this->client->get($this->path("code/{$code}"));
    }

    /**
     * Create coupon
     */
    public function create(array $data): array
    {
        return $this->client->post($this->path(), $data);
    }

    /**
     * Update coupon
     */
    public function updateCoupon(int $couponId, array $data): array
    {
        return $this->client->put($this->path($couponId), $data);
    }

    /**
     * Delete coupon
     */
    public function delete(int $couponId): array
    {
        return $this->client->delete($this->path($couponId));
    }

    /**
     * Duplicate coupon
     */
    public function duplicate(int $couponId): array
    {
        return $this->client->post($this->path("{$couponId}/duplicate"));
    }

    // === Activation ===

    /**
     * Activate coupon
     */
    public function activate(int $couponId): array
    {
        return $this->client->post($this->path("{$couponId}/activate"));
    }

    /**
     * Deactivate coupon
     */
    public function deactivate(int $couponId): array
    {
        return $this->client->post($this->path("{$couponId}/deactivate"));
    }

    /**
     * Get active coupons
     */
    public function active(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('active'), $params);
    }

    /**
     * Get expired coupons
     */
    public function expired(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('expired'), $params);
    }

    // === Validation ===

    /**
     * Validate coupon code
     */
    public function validate(string $code, array $context = []): array
    {
        return $this->client->post($this->path('validate'), array_merge(['code' => $code], $context));
    }

    /**
     * Apply coupon to cart/order
     */
    public function apply(string $code, int $orderId): array
    {
        return $this->client->post($this->path('apply'), [
            'code' => $code,
            'order_id' => $orderId
        ]);
    }

    /**
     * Remove coupon from cart/order
     */
    public function remove(int $orderId): array
    {
        return $this->client->post($this->path('remove'), ['order_id' => $orderId]);
    }

    /**
     * Calculate discount
     */
    public function calculateDiscount(string $code, array $items): array
    {
        return $this->client->post($this->path('calculate'), [
            'code' => $code,
            'items' => $items
        ]);
    }

    // === Usage ===

    /**
     * Get coupon usage history
     */
    public function usageHistory(int $couponId, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("{$couponId}/usage"), $params);
    }

    /**
     * Get user coupon usage
     */
    public function userUsage(int $userId, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("users/{$userId}/usage"), $params);
    }

    /**
     * Record manual usage
     */
    public function recordUsage(int $couponId, int $userId, int $orderId, float $discountAmount): array
    {
        return $this->client->post($this->path("{$couponId}/record-usage"), [
            'user_id' => $userId,
            'order_id' => $orderId,
            'discount_amount' => $discountAmount
        ]);
    }

    // === Restrictions ===

    /**
     * Get coupon restrictions
     */
    public function restrictions(int $couponId): array
    {
        return $this->client->get($this->path("{$couponId}/restrictions"));
    }

    /**
     * Update restrictions
     */
    public function updateRestrictions(int $couponId, array $restrictions): array
    {
        return $this->client->put($this->path("{$couponId}/restrictions"), $restrictions);
    }

    /**
     * Add product restriction
     */
    public function addProductRestriction(int $couponId, int $productId, string $type = 'include'): array
    {
        return $this->client->post($this->path("{$couponId}/restrictions/products"), [
            'product_id' => $productId,
            'type' => $type
        ]);
    }

    /**
     * Remove product restriction
     */
    public function removeProductRestriction(int $couponId, int $productId): array
    {
        return $this->client->delete($this->path("{$couponId}/restrictions/products/{$productId}"));
    }

    /**
     * Add user restriction
     */
    public function addUserRestriction(int $couponId, int $userId): array
    {
        return $this->client->post($this->path("{$couponId}/restrictions/users"), [
            'user_id' => $userId
        ]);
    }

    /**
     * Remove user restriction
     */
    public function removeUserRestriction(int $couponId, int $userId): array
    {
        return $this->client->delete($this->path("{$couponId}/restrictions/users/{$userId}"));
    }

    // === Bulk Operations ===

    /**
     * Generate bulk coupons
     */
    public function generateBulk(array $template, int $count, string $prefix = ''): array
    {
        return $this->client->post($this->path('generate-bulk'), [
            'template' => $template,
            'count' => $count,
            'prefix' => $prefix
        ]);
    }

    /**
     * Activate multiple coupons
     */
    public function activateBulk(array $couponIds): array
    {
        return $this->client->post($this->path('bulk/activate'), ['coupon_ids' => $couponIds]);
    }

    /**
     * Deactivate multiple coupons
     */
    public function deactivateBulk(array $couponIds): array
    {
        return $this->client->post($this->path('bulk/deactivate'), ['coupon_ids' => $couponIds]);
    }

    /**
     * Delete multiple coupons
     */
    public function deleteBulk(array $couponIds): array
    {
        return $this->client->delete($this->path('bulk'), ['coupon_ids' => $couponIds]);
    }

    // === User Coupons ===

    /**
     * Get available coupons for user
     */
    public function availableForUser(int $userId, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("users/{$userId}/available"), $params);
    }

    /**
     * Assign coupon to user
     */
    public function assignToUser(int $couponId, int $userId): array
    {
        return $this->client->post($this->path("{$couponId}/assign"), ['user_id' => $userId]);
    }

    /**
     * Assign coupon to multiple users
     */
    public function assignToUsers(int $couponId, array $userIds): array
    {
        return $this->client->post($this->path("{$couponId}/assign-bulk"), ['user_ids' => $userIds]);
    }

    // === Statistics ===

    /**
     * Get coupon statistics
     */
    public function statistics(array $params = []): array
    {
        return $this->client->get($this->path('statistics'), $params);
    }

    /**
     * Get coupon performance
     */
    public function performance(int $couponId): array
    {
        return $this->client->get($this->path("{$couponId}/performance"));
    }

    /**
     * Get revenue impact
     */
    public function revenueImpact(int $couponId, array $params = []): array
    {
        return $this->client->get($this->path("{$couponId}/revenue-impact"), $params);
    }

    /**
     * Get top performing coupons
     */
    public function topPerforming(int $limit = 10): array
    {
        return $this->client->get($this->path('top-performing'), ['limit' => $limit]);
    }

    // === Export/Import ===

    /**
     * Export coupons
     */
    public function export(string $format = 'csv', array $params = []): array
    {
        return $this->client->get($this->path('export'), array_merge($params, ['format' => $format]));
    }

    /**
     * Import coupons
     */
    public function import(string $filePath): array
    {
        return $this->client->upload($this->path('import'), $filePath, 'file');
    }
}
