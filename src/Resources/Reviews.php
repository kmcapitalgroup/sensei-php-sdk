<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK\Resources;

use Sensei\PartnerSDK\Support\PaginatedResponse;

/**
 * Reviews resource
 *
 * Manage product reviews and ratings
 */
class Reviews extends Resource
{
    protected string $basePath = 'v1/partners/reviews';

    // === Reviews ===

    /**
     * List all reviews
     */
    public function all(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path(), $params);
    }

    /**
     * Get reviews for product
     */
    public function forProduct(int $productId, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("products/{$productId}"), $params);
    }

    /**
     * Get review
     */
    public function get(int $reviewId): array
    {
        return $this->client->get($this->path($reviewId));
    }

    /**
     * Create review (on behalf of user)
     */
    public function create(int $productId, int $userId, array $data): array
    {
        return $this->client->post($this->path("products/{$productId}"), array_merge($data, [
            'user_id' => $userId
        ]));
    }

    /**
     * Update review
     */
    public function updateReview(int $reviewId, array $data): array
    {
        return $this->client->put($this->path($reviewId), $data);
    }

    /**
     * Delete review
     */
    public function delete(int $reviewId): array
    {
        return $this->client->delete($this->path($reviewId));
    }

    // === Moderation ===

    /**
     * Get pending reviews
     */
    public function pending(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('pending'), $params);
    }

    /**
     * Approve review
     */
    public function approve(int $reviewId): array
    {
        return $this->client->post($this->path("{$reviewId}/approve"));
    }

    /**
     * Reject review
     */
    public function reject(int $reviewId, ?string $reason = null): array
    {
        return $this->client->post($this->path("{$reviewId}/reject"), ['reason' => $reason]);
    }

    /**
     * Flag review
     */
    public function flag(int $reviewId, string $reason): array
    {
        return $this->client->post($this->path("{$reviewId}/flag"), ['reason' => $reason]);
    }

    /**
     * Get flagged reviews
     */
    public function flagged(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('flagged'), $params);
    }

    // === Responses ===

    /**
     * Respond to review
     */
    public function respond(int $reviewId, string $response): array
    {
        return $this->client->post($this->path("{$reviewId}/respond"), ['response' => $response]);
    }

    /**
     * Update response
     */
    public function updateResponse(int $reviewId, string $response): array
    {
        return $this->client->put($this->path("{$reviewId}/respond"), ['response' => $response]);
    }

    /**
     * Delete response
     */
    public function deleteResponse(int $reviewId): array
    {
        return $this->client->delete($this->path("{$reviewId}/respond"));
    }

    // === Helpfulness ===

    /**
     * Mark review as helpful
     */
    public function markHelpful(int $reviewId, int $userId): array
    {
        return $this->client->post($this->path("{$reviewId}/helpful"), ['user_id' => $userId]);
    }

    /**
     * Mark review as not helpful
     */
    public function markNotHelpful(int $reviewId, int $userId): array
    {
        return $this->client->post($this->path("{$reviewId}/not-helpful"), ['user_id' => $userId]);
    }

    // === Product Ratings ===

    /**
     * Get product rating summary
     */
    public function productRating(int $productId): array
    {
        return $this->client->get($this->path("products/{$productId}/rating"));
    }

    /**
     * Get rating distribution
     */
    public function ratingDistribution(int $productId): array
    {
        return $this->client->get($this->path("products/{$productId}/distribution"));
    }

    // === User Reviews ===

    /**
     * Get user's reviews
     */
    public function userReviews(int $userId, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("users/{$userId}"), $params);
    }

    /**
     * Check if user can review product
     */
    public function canReview(int $productId, int $userId): array
    {
        return $this->client->get($this->path("products/{$productId}/can-review"), ['user_id' => $userId]);
    }

    // === Media ===

    /**
     * Upload review image
     */
    public function uploadImage(int $reviewId, string $filePath): array
    {
        return $this->client->upload($this->path("{$reviewId}/images"), $filePath, 'image');
    }

    /**
     * Delete review image
     */
    public function deleteImage(int $reviewId, int $imageId): array
    {
        return $this->client->delete($this->path("{$reviewId}/images/{$imageId}"));
    }

    /**
     * Upload review video
     */
    public function uploadVideo(int $reviewId, string $filePath): array
    {
        return $this->client->upload($this->path("{$reviewId}/videos"), $filePath, 'video');
    }

    // === Statistics ===

    /**
     * Get review statistics
     */
    public function statistics(array $params = []): array
    {
        return $this->client->get($this->path('statistics'), $params);
    }

    /**
     * Get product review stats
     */
    public function productStats(int $productId): array
    {
        return $this->client->get($this->path("products/{$productId}/stats"));
    }

    /**
     * Get recent reviews widget data
     */
    public function recentWidget(int $limit = 5): array
    {
        return $this->client->get($this->path('widgets/recent'), ['limit' => $limit]);
    }

    /**
     * Get top rated products
     */
    public function topRated(int $limit = 10): array
    {
        return $this->client->get($this->path('top-rated'), ['limit' => $limit]);
    }

    // === Import/Export ===

    /**
     * Export reviews
     */
    public function export(string $format = 'csv', array $params = []): array
    {
        return $this->client->get($this->path('export'), array_merge($params, ['format' => $format]));
    }

    /**
     * Import reviews
     */
    public function import(string $filePath): array
    {
        return $this->client->upload($this->path('import'), $filePath, 'file');
    }

    // === Settings ===

    /**
     * Get review settings
     */
    public function settings(): array
    {
        return $this->client->get($this->path('settings'));
    }

    /**
     * Update review settings
     */
    public function updateSettings(array $settings): array
    {
        return $this->client->put($this->path('settings'), $settings);
    }
}
