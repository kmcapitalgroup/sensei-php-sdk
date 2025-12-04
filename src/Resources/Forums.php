<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK\Resources;

use Sensei\PartnerSDK\Support\PaginatedResponse;

/**
 * Forums resource
 *
 * Manage discussion forums, topics, and replies
 */
class Forums extends Resource
{
    protected string $basePath = 'partner/forums';

    // === Categories ===

    /**
     * List forum categories
     */
    public function categories(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('categories'), $params);
    }

    /**
     * Get category
     */
    public function category(int $categoryId): array
    {
        return $this->client->get($this->path("categories/{$categoryId}"));
    }

    /**
     * Create category
     */
    public function createCategory(array $data): array
    {
        return $this->client->post($this->path('categories'), $data);
    }

    /**
     * Update category
     */
    public function updateCategory(int $categoryId, array $data): array
    {
        return $this->client->put($this->path("categories/{$categoryId}"), $data);
    }

    /**
     * Delete category
     */
    public function deleteCategory(int $categoryId): array
    {
        return $this->client->delete($this->path("categories/{$categoryId}"));
    }

    /**
     * Reorder categories
     */
    public function reorderCategories(array $order): array
    {
        return $this->client->post($this->path('categories/reorder'), ['order' => $order]);
    }

    // === Topics ===

    /**
     * List topics
     */
    public function topics(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('topics'), $params);
    }

    /**
     * Get topics in category
     */
    public function categoryTopics(int $categoryId, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("categories/{$categoryId}/topics"), $params);
    }

    /**
     * Get topic
     */
    public function topic(int $topicId): array
    {
        return $this->client->get($this->path("topics/{$topicId}"));
    }

    /**
     * Create topic
     */
    public function createTopic(int $categoryId, int $userId, array $data): array
    {
        return $this->client->post($this->path("categories/{$categoryId}/topics"), array_merge($data, [
            'user_id' => $userId
        ]));
    }

    /**
     * Update topic
     */
    public function updateTopic(int $topicId, array $data): array
    {
        return $this->client->put($this->path("topics/{$topicId}"), $data);
    }

    /**
     * Delete topic
     */
    public function deleteTopic(int $topicId): array
    {
        return $this->client->delete($this->path("topics/{$topicId}"));
    }

    /**
     * Pin topic
     */
    public function pinTopic(int $topicId): array
    {
        return $this->client->post($this->path("topics/{$topicId}/pin"));
    }

    /**
     * Unpin topic
     */
    public function unpinTopic(int $topicId): array
    {
        return $this->client->post($this->path("topics/{$topicId}/unpin"));
    }

    /**
     * Lock topic
     */
    public function lockTopic(int $topicId): array
    {
        return $this->client->post($this->path("topics/{$topicId}/lock"));
    }

    /**
     * Unlock topic
     */
    public function unlockTopic(int $topicId): array
    {
        return $this->client->post($this->path("topics/{$topicId}/unlock"));
    }

    /**
     * Move topic to another category
     */
    public function moveTopic(int $topicId, int $categoryId): array
    {
        return $this->client->post($this->path("topics/{$topicId}/move"), ['category_id' => $categoryId]);
    }

    // === Posts/Replies ===

    /**
     * Get topic posts
     */
    public function posts(int $topicId, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("topics/{$topicId}/posts"), $params);
    }

    /**
     * Get post
     */
    public function post(int $postId): array
    {
        return $this->client->get($this->path("posts/{$postId}"));
    }

    /**
     * Create post (reply)
     */
    public function createPost(int $topicId, int $userId, array $data): array
    {
        return $this->client->post($this->path("topics/{$topicId}/posts"), array_merge($data, [
            'user_id' => $userId
        ]));
    }

    /**
     * Update post
     */
    public function updatePost(int $postId, array $data): array
    {
        return $this->client->put($this->path("posts/{$postId}"), $data);
    }

    /**
     * Delete post
     */
    public function deletePost(int $postId): array
    {
        return $this->client->delete($this->path("posts/{$postId}"));
    }

    /**
     * Mark post as solution
     */
    public function markAsSolution(int $postId): array
    {
        return $this->client->post($this->path("posts/{$postId}/solution"));
    }

    /**
     * Unmark post as solution
     */
    public function unmarkAsSolution(int $postId): array
    {
        return $this->client->delete($this->path("posts/{$postId}/solution"));
    }

    // === Reactions ===

    /**
     * Like post
     */
    public function likePost(int $postId, int $userId): array
    {
        return $this->client->post($this->path("posts/{$postId}/like"), ['user_id' => $userId]);
    }

    /**
     * Unlike post
     */
    public function unlikePost(int $postId, int $userId): array
    {
        return $this->client->delete($this->path("posts/{$postId}/like"), ['user_id' => $userId]);
    }

    /**
     * Get post likes
     */
    public function postLikes(int $postId): array
    {
        return $this->client->get($this->path("posts/{$postId}/likes"));
    }

    // === Subscriptions ===

    /**
     * Subscribe user to topic
     */
    public function subscribeTopic(int $topicId, int $userId): array
    {
        return $this->client->post($this->path("topics/{$topicId}/subscribe"), ['user_id' => $userId]);
    }

    /**
     * Unsubscribe user from topic
     */
    public function unsubscribeTopic(int $topicId, int $userId): array
    {
        return $this->client->delete($this->path("topics/{$topicId}/subscribe"), ['user_id' => $userId]);
    }

    /**
     * Get user subscriptions
     */
    public function userSubscriptions(int $userId): PaginatedResponse
    {
        return $this->paginate($this->path("users/{$userId}/subscriptions"));
    }

    // === Moderation ===

    /**
     * Get reported posts
     */
    public function reportedPosts(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('moderation/reported'), $params);
    }

    /**
     * Report post
     */
    public function reportPost(int $postId, int $userId, string $reason): array
    {
        return $this->client->post($this->path("posts/{$postId}/report"), [
            'user_id' => $userId,
            'reason' => $reason
        ]);
    }

    /**
     * Approve reported post
     */
    public function approveReport(int $reportId): array
    {
        return $this->client->post($this->path("moderation/reports/{$reportId}/approve"));
    }

    /**
     * Dismiss report
     */
    public function dismissReport(int $reportId): array
    {
        return $this->client->post($this->path("moderation/reports/{$reportId}/dismiss"));
    }

    /**
     * Ban user from forums
     */
    public function banUser(int $userId, ?string $reason = null, ?string $until = null): array
    {
        return $this->client->post($this->path("moderation/ban"), [
            'user_id' => $userId,
            'reason' => $reason,
            'until' => $until
        ]);
    }

    /**
     * Unban user from forums
     */
    public function unbanUser(int $userId): array
    {
        return $this->client->post($this->path("moderation/unban"), ['user_id' => $userId]);
    }

    // === Search ===

    /**
     * Search forums
     */
    public function search(string $query, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('search'), array_merge($params, ['q' => $query]));
    }

    // === Statistics ===

    /**
     * Get forum statistics
     */
    public function statistics(): array
    {
        return $this->client->get($this->path('statistics'));
    }

    /**
     * Get user forum stats
     */
    public function userStats(int $userId): array
    {
        return $this->client->get($this->path("users/{$userId}/stats"));
    }

    /**
     * Get trending topics
     */
    public function trending(array $params = []): array
    {
        return $this->client->get($this->path('trending'), $params);
    }

    // === Settings ===

    /**
     * Get forum settings
     */
    public function settings(): array
    {
        return $this->client->get($this->path('settings'));
    }

    /**
     * Update forum settings
     */
    public function updateSettings(array $settings): array
    {
        return $this->client->put($this->path('settings'), $settings);
    }
}
