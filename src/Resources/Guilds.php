<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK\Resources;

use Sensei\PartnerSDK\Support\PaginatedResponse;

/**
 * Guild/Community management resource
 *
 * Create and manage guilds, members, roles, and channels
 *
 * IMPORTANT: This resource requires:
 * - Bearer Token authentication (user token from signupAndLink/loginAndLink)
 * - Tenant configuration in the client
 *
 * The routes use /v1/{tenant}/guilds for user-owned operations.
 */
class Guilds extends Resource
{
    protected string $basePath = 'v1/{tenant}/guilds';

    /**
     * List all guilds
     */
    public function all(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path(), $params);
    }

    /**
     * Get a specific guild
     */
    public function get(int $id): array
    {
        return $this->find($id);
    }

    /**
     * Create a new guild
     */
    public function create(array $data): array
    {
        return $this->store($data);
    }

    /**
     * Update a guild
     */
    public function updateGuild(int $id, array $data): array
    {
        return $this->update($id, $data);
    }

    /**
     * Delete a guild
     */
    public function delete(int $id): array
    {
        return $this->destroy($id);
    }

    /**
     * Get guild statistics
     */
    public function stats(int $guildId): array
    {
        return $this->client->get($this->path("{$guildId}/stats"));
    }

    // === Members ===

    /**
     * List guild members
     */
    public function members(int $guildId, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("{$guildId}/members"), $params);
    }

    /**
     * Get a guild member
     */
    public function member(int $guildId, int $userId): array
    {
        return $this->client->get($this->path("{$guildId}/members/{$userId}"));
    }

    /**
     * Add member to guild
     */
    public function addMember(int $guildId, int $userId, array $data = []): array
    {
        return $this->client->post($this->path("{$guildId}/members"), array_merge($data, ['user_id' => $userId]));
    }

    /**
     * Remove member from guild
     */
    public function removeMember(int $guildId, int $userId): array
    {
        return $this->client->delete($this->path("{$guildId}/members/{$userId}"));
    }

    /**
     * Update member role
     */
    public function updateMemberRole(int $guildId, int $userId, int $roleId): array
    {
        return $this->client->put($this->path("{$guildId}/members/{$userId}/role"), ['role_id' => $roleId]);
    }

    /**
     * Ban member
     */
    public function banMember(int $guildId, int $userId, string $reason = ''): array
    {
        return $this->client->post($this->path("{$guildId}/members/{$userId}/ban"), ['reason' => $reason]);
    }

    /**
     * Unban member
     */
    public function unbanMember(int $guildId, int $userId): array
    {
        return $this->client->post($this->path("{$guildId}/members/{$userId}/unban"));
    }

    /**
     * Get banned members
     */
    public function bannedMembers(int $guildId): array
    {
        return $this->client->get($this->path("{$guildId}/bans"));
    }

    /**
     * Mute member
     */
    public function muteMember(int $guildId, int $userId, int $durationMinutes): array
    {
        return $this->client->post($this->path("{$guildId}/members/{$userId}/mute"), [
            'duration_minutes' => $durationMinutes
        ]);
    }

    /**
     * Unmute member
     */
    public function unmuteMember(int $guildId, int $userId): array
    {
        return $this->client->post($this->path("{$guildId}/members/{$userId}/unmute"));
    }

    // === Roles ===

    /**
     * List guild roles
     */
    public function roles(int $guildId): array
    {
        return $this->client->get($this->path("{$guildId}/roles"));
    }

    /**
     * Create role
     */
    public function createRole(int $guildId, array $data): array
    {
        return $this->client->post($this->path("{$guildId}/roles"), $data);
    }

    /**
     * Update role
     */
    public function updateRole(int $guildId, int $roleId, array $data): array
    {
        return $this->client->put($this->path("{$guildId}/roles/{$roleId}"), $data);
    }

    /**
     * Delete role
     */
    public function deleteRole(int $guildId, int $roleId): array
    {
        return $this->client->delete($this->path("{$guildId}/roles/{$roleId}"));
    }

    /**
     * Reorder roles
     */
    public function reorderRoles(int $guildId, array $order): array
    {
        return $this->client->post($this->path("{$guildId}/roles/reorder"), ['order' => $order]);
    }

    // === Channels ===

    /**
     * List guild channels
     */
    public function channels(int $guildId): array
    {
        return $this->client->get($this->path("{$guildId}/channels"));
    }

    /**
     * Get a channel
     */
    public function channel(int $guildId, int $channelId): array
    {
        return $this->client->get($this->path("{$guildId}/channels/{$channelId}"));
    }

    /**
     * Create channel
     */
    public function createChannel(int $guildId, array $data): array
    {
        return $this->client->post($this->path("{$guildId}/channels"), $data);
    }

    /**
     * Update channel
     */
    public function updateChannel(int $guildId, int $channelId, array $data): array
    {
        return $this->client->put($this->path("{$guildId}/channels/{$channelId}"), $data);
    }

    /**
     * Delete channel
     */
    public function deleteChannel(int $guildId, int $channelId): array
    {
        return $this->client->delete($this->path("{$guildId}/channels/{$channelId}"));
    }

    /**
     * Reorder channels
     */
    public function reorderChannels(int $guildId, array $order): array
    {
        return $this->client->post($this->path("{$guildId}/channels/reorder"), ['order' => $order]);
    }

    // === Channel Categories ===

    /**
     * List channel categories
     */
    public function categories(int $guildId): array
    {
        return $this->client->get($this->path("{$guildId}/categories"));
    }

    /**
     * Create category
     */
    public function createCategory(int $guildId, array $data): array
    {
        return $this->client->post($this->path("{$guildId}/categories"), $data);
    }

    /**
     * Update category
     */
    public function updateCategory(int $guildId, int $categoryId, array $data): array
    {
        return $this->client->put($this->path("{$guildId}/categories/{$categoryId}"), $data);
    }

    /**
     * Delete category
     */
    public function deleteCategory(int $guildId, int $categoryId): array
    {
        return $this->client->delete($this->path("{$guildId}/categories/{$categoryId}"));
    }

    // === Invites ===

    /**
     * List guild invites
     */
    public function invites(int $guildId): array
    {
        return $this->client->get($this->path("{$guildId}/invites"));
    }

    /**
     * Create invite
     */
    public function createInvite(int $guildId, array $data = []): array
    {
        return $this->client->post($this->path("{$guildId}/invites"), $data);
    }

    /**
     * Delete invite
     */
    public function deleteInvite(int $guildId, string $inviteCode): array
    {
        return $this->client->delete($this->path("{$guildId}/invites/{$inviteCode}"));
    }

    /**
     * Get invite info
     */
    public function inviteInfo(string $inviteCode): array
    {
        return $this->client->get("partner/invites/{$inviteCode}");
    }

    // === Settings ===

    /**
     * Get guild settings
     */
    public function settings(int $guildId): array
    {
        return $this->client->get($this->path("{$guildId}/settings"));
    }

    /**
     * Update guild settings
     */
    public function updateSettings(int $guildId, array $settings): array
    {
        return $this->client->put($this->path("{$guildId}/settings"), $settings);
    }

    // === Media ===

    /**
     * Upload guild icon
     */
    public function uploadIcon(int $guildId, string $filePath): array
    {
        return $this->client->upload($this->path("{$guildId}/icon"), $filePath, 'icon');
    }

    /**
     * Upload guild banner
     */
    public function uploadBanner(int $guildId, string $filePath): array
    {
        return $this->client->upload($this->path("{$guildId}/banner"), $filePath, 'banner');
    }

    // === Activity ===

    /**
     * Get guild activity feed
     */
    public function activity(int $guildId, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("{$guildId}/activity"), $params);
    }

    /**
     * Get guild leaderboard
     */
    public function leaderboard(int $guildId, array $params = []): array
    {
        return $this->client->get($this->path("{$guildId}/leaderboard"), $params);
    }

    // === Permissions ===

    /**
     * Get available permissions
     */
    public function availablePermissions(): array
    {
        return $this->client->get($this->path('permissions'));
    }

    /**
     * Get member permissions
     */
    public function memberPermissions(int $guildId, int $userId): array
    {
        return $this->client->get($this->path("{$guildId}/members/{$userId}/permissions"));
    }
}
