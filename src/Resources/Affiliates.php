<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK\Resources;

use Sensei\PartnerSDK\Support\PaginatedResponse;

/**
 * Affiliates resource
 *
 * Manage affiliate program, referrals, and commissions
 */
class Affiliates extends Resource
{
    protected string $basePath = 'partner/affiliates';

    // === Program Settings ===

    /**
     * Get affiliate program settings
     */
    public function settings(): array
    {
        return $this->client->get($this->path('settings'));
    }

    /**
     * Update affiliate program settings
     */
    public function updateSettings(array $settings): array
    {
        return $this->client->put($this->path('settings'), $settings);
    }

    /**
     * Enable affiliate program
     */
    public function enable(): array
    {
        return $this->client->post($this->path('enable'));
    }

    /**
     * Disable affiliate program
     */
    public function disable(): array
    {
        return $this->client->post($this->path('disable'));
    }

    // === Affiliates ===

    /**
     * List all affiliates
     */
    public function all(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path(), $params);
    }

    /**
     * Get an affiliate
     */
    public function get(int $affiliateId): array
    {
        return $this->client->get($this->path($affiliateId));
    }

    /**
     * Create affiliate (enroll user)
     */
    public function create(int $userId, array $data = []): array
    {
        return $this->client->post($this->path(), array_merge($data, ['user_id' => $userId]));
    }

    /**
     * Update affiliate
     */
    public function updateAffiliate(int $affiliateId, array $data): array
    {
        return $this->client->put($this->path($affiliateId), $data);
    }

    /**
     * Approve affiliate
     */
    public function approve(int $affiliateId): array
    {
        return $this->client->post($this->path("{$affiliateId}/approve"));
    }

    /**
     * Reject affiliate
     */
    public function reject(int $affiliateId, ?string $reason = null): array
    {
        return $this->client->post($this->path("{$affiliateId}/reject"), ['reason' => $reason]);
    }

    /**
     * Suspend affiliate
     */
    public function suspend(int $affiliateId, string $reason): array
    {
        return $this->client->post($this->path("{$affiliateId}/suspend"), ['reason' => $reason]);
    }

    /**
     * Reactivate affiliate
     */
    public function reactivate(int $affiliateId): array
    {
        return $this->client->post($this->path("{$affiliateId}/reactivate"));
    }

    /**
     * Get affiliate dashboard data
     */
    public function dashboard(int $affiliateId): array
    {
        return $this->client->get($this->path("{$affiliateId}/dashboard"));
    }

    // === Referral Links ===

    /**
     * Get affiliate's referral links
     */
    public function links(int $affiliateId): array
    {
        return $this->client->get($this->path("{$affiliateId}/links"));
    }

    /**
     * Create referral link
     */
    public function createLink(int $affiliateId, array $data): array
    {
        return $this->client->post($this->path("{$affiliateId}/links"), $data);
    }

    /**
     * Update referral link
     */
    public function updateLink(int $affiliateId, int $linkId, array $data): array
    {
        return $this->client->put($this->path("{$affiliateId}/links/{$linkId}"), $data);
    }

    /**
     * Delete referral link
     */
    public function deleteLink(int $affiliateId, int $linkId): array
    {
        return $this->client->delete($this->path("{$affiliateId}/links/{$linkId}"));
    }

    /**
     * Get link statistics
     */
    public function linkStats(int $affiliateId, int $linkId): array
    {
        return $this->client->get($this->path("{$affiliateId}/links/{$linkId}/stats"));
    }

    // === Coupons ===

    /**
     * Get affiliate's coupons
     */
    public function coupons(int $affiliateId): array
    {
        return $this->client->get($this->path("{$affiliateId}/coupons"));
    }

    /**
     * Create affiliate coupon
     */
    public function createCoupon(int $affiliateId, array $data): array
    {
        return $this->client->post($this->path("{$affiliateId}/coupons"), $data);
    }

    /**
     * Update affiliate coupon
     */
    public function updateCoupon(int $affiliateId, int $couponId, array $data): array
    {
        return $this->client->put($this->path("{$affiliateId}/coupons/{$couponId}"), $data);
    }

    /**
     * Delete affiliate coupon
     */
    public function deleteCoupon(int $affiliateId, int $couponId): array
    {
        return $this->client->delete($this->path("{$affiliateId}/coupons/{$couponId}"));
    }

    // === Referrals ===

    /**
     * Get affiliate's referrals
     */
    public function referrals(int $affiliateId, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("{$affiliateId}/referrals"), $params);
    }

    /**
     * Get a referral
     */
    public function referral(int $referralId): array
    {
        return $this->client->get($this->path("referrals/{$referralId}"));
    }

    /**
     * Track referral visit
     */
    public function trackVisit(string $referralCode, array $data = []): array
    {
        return $this->client->post($this->path('track/visit'), array_merge($data, ['code' => $referralCode]));
    }

    /**
     * Track referral conversion
     */
    public function trackConversion(string $referralCode, int $userId, array $data = []): array
    {
        return $this->client->post($this->path('track/conversion'), array_merge($data, [
            'code' => $referralCode,
            'user_id' => $userId
        ]));
    }

    // === Commissions ===

    /**
     * Get affiliate's commissions
     */
    public function commissions(int $affiliateId, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("{$affiliateId}/commissions"), $params);
    }

    /**
     * Get pending commissions
     */
    public function pendingCommissions(int $affiliateId): array
    {
        return $this->client->get($this->path("{$affiliateId}/commissions/pending"));
    }

    /**
     * Approve commission
     */
    public function approveCommission(int $commissionId): array
    {
        return $this->client->post($this->path("commissions/{$commissionId}/approve"));
    }

    /**
     * Reject commission
     */
    public function rejectCommission(int $commissionId, string $reason): array
    {
        return $this->client->post($this->path("commissions/{$commissionId}/reject"), ['reason' => $reason]);
    }

    /**
     * Create manual commission
     */
    public function createCommission(int $affiliateId, array $data): array
    {
        return $this->client->post($this->path("{$affiliateId}/commissions"), $data);
    }

    // === Commission Tiers ===

    /**
     * Get commission tiers
     */
    public function tiers(): array
    {
        return $this->client->get($this->path('tiers'));
    }

    /**
     * Create commission tier
     */
    public function createTier(array $data): array
    {
        return $this->client->post($this->path('tiers'), $data);
    }

    /**
     * Update commission tier
     */
    public function updateTier(int $tierId, array $data): array
    {
        return $this->client->put($this->path("tiers/{$tierId}"), $data);
    }

    /**
     * Delete commission tier
     */
    public function deleteTier(int $tierId): array
    {
        return $this->client->delete($this->path("tiers/{$tierId}"));
    }

    /**
     * Assign tier to affiliate
     */
    public function assignTier(int $affiliateId, int $tierId): array
    {
        return $this->client->post($this->path("{$affiliateId}/tier"), ['tier_id' => $tierId]);
    }

    // === Payouts ===

    /**
     * Get affiliate's payouts
     */
    public function payouts(int $affiliateId, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("{$affiliateId}/payouts"), $params);
    }

    /**
     * Request payout
     */
    public function requestPayout(int $affiliateId): array
    {
        return $this->client->post($this->path("{$affiliateId}/payouts/request"));
    }

    /**
     * Process payout
     */
    public function processPayout(int $payoutId): array
    {
        return $this->client->post($this->path("payouts/{$payoutId}/process"));
    }

    /**
     * Cancel payout
     */
    public function cancelPayout(int $payoutId): array
    {
        return $this->client->post($this->path("payouts/{$payoutId}/cancel"));
    }

    /**
     * Get pending payouts (admin)
     */
    public function pendingPayouts(): PaginatedResponse
    {
        return $this->paginate($this->path('payouts/pending'));
    }

    /**
     * Get affiliate balance
     */
    public function balance(int $affiliateId): array
    {
        return $this->client->get($this->path("{$affiliateId}/balance"));
    }

    // === Marketing Materials ===

    /**
     * Get marketing materials
     */
    public function materials(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('materials'), $params);
    }

    /**
     * Create marketing material
     */
    public function createMaterial(array $data): array
    {
        return $this->client->post($this->path('materials'), $data);
    }

    /**
     * Update marketing material
     */
    public function updateMaterial(int $materialId, array $data): array
    {
        return $this->client->put($this->path("materials/{$materialId}"), $data);
    }

    /**
     * Delete marketing material
     */
    public function deleteMaterial(int $materialId): array
    {
        return $this->client->delete($this->path("materials/{$materialId}"));
    }

    /**
     * Upload material asset
     */
    public function uploadMaterialAsset(int $materialId, string $filePath): array
    {
        return $this->client->upload($this->path("materials/{$materialId}/assets"), $filePath, 'file');
    }

    // === Statistics & Reports ===

    /**
     * Get affiliate statistics
     */
    public function statistics(int $affiliateId, array $params = []): array
    {
        return $this->client->get($this->path("{$affiliateId}/statistics"), $params);
    }

    /**
     * Get program statistics (admin)
     */
    public function programStatistics(array $params = []): array
    {
        return $this->client->get($this->path('statistics'), $params);
    }

    /**
     * Get leaderboard
     */
    public function leaderboard(array $params = []): array
    {
        return $this->client->get($this->path('leaderboard'), $params);
    }

    /**
     * Export affiliates report
     */
    public function exportReport(string $format = 'csv', array $params = []): array
    {
        return $this->client->get($this->path('export'), array_merge($params, ['format' => $format]));
    }

    // === Applications ===

    /**
     * Get pending applications
     */
    public function applications(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('applications'), $params);
    }

    /**
     * Submit affiliate application
     */
    public function apply(int $userId, array $data): array
    {
        return $this->client->post($this->path('apply'), array_merge($data, ['user_id' => $userId]));
    }
}
