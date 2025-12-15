<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK\Resources;

use Sensei\PartnerSDK\Support\PaginatedResponse;

/**
 * User/Customer management resource
 *
 * Manage customers, students, and their access
 */
class Users extends Resource
{
    protected string $basePath = 'v1/partners/users';

    /**
     * List all users/customers
     */
    public function all(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path(), $params);
    }

    /**
     * Get a specific user
     */
    public function get(int $id): array
    {
        return $this->find($id);
    }

    /**
     * Search users
     */
    public function search(string $query, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('search'), array_merge($params, ['q' => $query]));
    }

    /**
     * Get user by email
     */
    public function findByEmail(string $email): array
    {
        return $this->client->get($this->path('by-email'), ['email' => $email]);
    }

    /**
     * Create a user (invite)
     */
    public function create(array $data): array
    {
        return $this->store($data);
    }

    /**
     * Update a user
     */
    public function updateUser(int $id, array $data): array
    {
        return $this->update($id, $data);
    }

    /**
     * Delete a user
     */
    public function delete(int $id): array
    {
        return $this->destroy($id);
    }

    /**
     * Get user's subscriptions
     */
    public function subscriptions(int $userId, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("{$userId}/subscriptions"), $params);
    }

    /**
     * Get user's active subscriptions
     */
    public function activeSubscriptions(int $userId): array
    {
        return $this->client->get($this->path("{$userId}/subscriptions/active"));
    }

    /**
     * Get user's purchase history
     */
    public function purchases(int $userId, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("{$userId}/purchases"), $params);
    }

    /**
     * Get user's payments
     */
    public function payments(int $userId, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("{$userId}/payments"), $params);
    }

    /**
     * Get user's invoices
     */
    public function invoices(int $userId, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("{$userId}/invoices"), $params);
    }

    /**
     * Get user's progress (for courses)
     */
    public function progress(int $userId, ?int $productId = null): array
    {
        $params = $productId ? ['product_id' => $productId] : [];
        return $this->client->get($this->path("{$userId}/progress"), $params);
    }

    /**
     * Reset user's progress for a product
     */
    public function resetProgress(int $userId, int $productId): array
    {
        return $this->client->post($this->path("{$userId}/progress/reset"), ['product_id' => $productId]);
    }

    /**
     * Get user's activity
     */
    public function activity(int $userId, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("{$userId}/activity"), $params);
    }

    /**
     * Get user's engagement metrics
     */
    public function engagement(int $userId): array
    {
        return $this->client->get($this->path("{$userId}/engagement"));
    }

    /**
     * Suspend a user
     */
    public function suspend(int $userId, ?string $reason = null): array
    {
        return $this->client->post($this->path("{$userId}/suspend"), ['reason' => $reason]);
    }

    /**
     * Unsuspend a user
     */
    public function unsuspend(int $userId): array
    {
        return $this->client->post($this->path("{$userId}/unsuspend"));
    }

    /**
     * Ban a user
     */
    public function ban(int $userId, string $reason): array
    {
        return $this->client->post($this->path("{$userId}/ban"), ['reason' => $reason]);
    }

    /**
     * Unban a user
     */
    public function unban(int $userId): array
    {
        return $this->client->post($this->path("{$userId}/unban"));
    }

    /**
     * Send password reset email
     */
    public function sendPasswordReset(int $userId): array
    {
        return $this->client->post($this->path("{$userId}/password-reset"));
    }

    /**
     * Verify user's email
     */
    public function verifyEmail(int $userId): array
    {
        return $this->client->post($this->path("{$userId}/verify-email"));
    }

    /**
     * Resend verification email
     */
    public function resendVerification(int $userId): array
    {
        return $this->client->post($this->path("{$userId}/resend-verification"));
    }

    /**
     * Add note to user
     */
    public function addNote(int $userId, string $note): array
    {
        return $this->client->post($this->path("{$userId}/notes"), ['note' => $note]);
    }

    /**
     * Get user notes
     */
    public function notes(int $userId): array
    {
        return $this->client->get($this->path("{$userId}/notes"));
    }

    /**
     * Delete a note
     */
    public function deleteNote(int $userId, int $noteId): array
    {
        return $this->client->delete($this->path("{$userId}/notes/{$noteId}"));
    }

    /**
     * Add tag to user
     */
    public function addTag(int $userId, string $tag): array
    {
        return $this->client->post($this->path("{$userId}/tags"), ['tag' => $tag]);
    }

    /**
     * Remove tag from user
     */
    public function removeTag(int $userId, string $tag): array
    {
        return $this->client->delete($this->path("{$userId}/tags/{$tag}"));
    }

    /**
     * Get user's tags
     */
    public function tags(int $userId): array
    {
        return $this->client->get($this->path("{$userId}/tags"));
    }

    /**
     * Import users from CSV
     */
    public function import(string $filePath, array $options = []): array
    {
        return $this->client->upload($this->path('import'), $filePath, 'file', $options);
    }

    /**
     * Export users
     */
    public function export(string $format = 'csv', array $params = []): array
    {
        return $this->client->get($this->path('export'), array_merge($params, ['format' => $format]));
    }

    /**
     * Get user segments
     */
    public function segments(): array
    {
        return $this->client->get($this->path('segments'));
    }

    /**
     * Get users in a segment
     */
    public function usersInSegment(int $segmentId, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("segments/{$segmentId}/users"), $params);
    }

    /**
     * Create a segment
     */
    public function createSegment(array $data): array
    {
        return $this->client->post($this->path('segments'), $data);
    }

    /**
     * Update a segment
     */
    public function updateSegment(int $segmentId, array $data): array
    {
        return $this->client->put($this->path("segments/{$segmentId}"), $data);
    }

    /**
     * Delete a segment
     */
    public function deleteSegment(int $segmentId): array
    {
        return $this->client->delete($this->path("segments/{$segmentId}"));
    }

    /**
     * Get customer lifetime value
     */
    public function lifetimeValue(int $userId): array
    {
        return $this->client->get($this->path("{$userId}/ltv"));
    }

    /**
     * Signup and link a new user to the partner's tenant
     *
     * This method creates a new user account and automatically links them
     * to the partner's tenant guild. Used for white-label partner integrations.
     *
     * IMPORTANT: This endpoint requires Partner API Key authentication.
     * The user is automatically verified and linked to the partner's tenant.
     *
     * @param array $data User registration data:
     *   - name (required): User's full name
     *   - email (required): User's email address
     *   - password (required): User's password
     *   - password_confirmation (required): Password confirmation
     *   - faction_id (optional): Specific guild ID within tenant to join
     *
     * @return array Response containing:
     *   - token: Authentication token for the new user
     *   - user: User data
     *   - guild: Guild info (id, name, slug) or null
     *   - tenant: Tenant info (id, name)
     *   - message: Success message
     *
     * @example
     * $response = $client->users->signupAndLink([
     *     'name' => 'John Doe',
     *     'email' => 'john@example.com',
     *     'password' => 'SecureP@ss123!',
     *     'password_confirmation' => 'SecureP@ss123!',
     * ]);
     *
     * // Store the user's token for subsequent requests
     * $userToken = $response['token'];
     */
    public function signupAndLink(array $data): array
    {
        // This endpoint is at /api/senseitemple/signup-and-link
        // not under the basePath (v1/partners/), so we call the client directly
        // Base URL should include /api, so we just use 'senseitemple/signup-and-link'
        return $this->client->post('senseitemple/signup-and-link', $data);
    }
}
