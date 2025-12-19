<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK\Resources;

use Sensei\PartnerSDK\Support\PaginatedResponse;

/**
 * Alliance management resource
 *
 * Alliances are federations of guilds that can cooperate,
 * share treasuries, and engage in wars against other alliances.
 */
class Alliances extends Resource
{
    protected string $basePath = 'v1/{tenant}/alliances';

    // =====================================
    // Alliance CRUD
    // =====================================

    /**
     * List all alliances
     *
     * @param array $params Query parameters:
     *   - search: Filter by name/description
     *   - sort_by: 'rank' or 'created_at'
     *   - per_page: Items per page
     */
    public function all(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path(), $params);
    }

    /**
     * Get a specific alliance by ID or slug
     */
    public function get(int|string $alliance): array
    {
        return $this->client->get($this->path($alliance));
    }

    /**
     * Create a new alliance
     *
     * @param array $data Alliance data:
     *   - name: string (required) Alliance name
     *   - slug: string (optional) URL-friendly slug
     *   - description: string (optional)
     *   - logo_url: string (optional)
     *   - banner_url: string (optional)
     *   - founder_guild_id: int (required) Guild creating the alliance
     *   - max_guilds: int (optional, default 10)
     *   - requires_approval: bool (optional, default true)
     *   - min_guild_level: int (optional, default 1)
     *   - settings: array (optional)
     */
    public function create(array $data): array
    {
        return $this->client->post($this->path(), $data);
    }

    /**
     * Update an alliance
     */
    public function update(int|string $alliance, array $data): array
    {
        return $this->client->put($this->path($alliance), $data);
    }

    /**
     * Dissolve (delete) an alliance
     *
     * Cannot dissolve during an active war.
     */
    public function dissolve(int|string $alliance): array
    {
        return $this->client->delete($this->path($alliance));
    }

    /**
     * Get alliance leaderboard
     */
    public function leaderboard(int $limit = 100): array
    {
        return $this->client->get($this->path('leaderboard'), ['limit' => $limit]);
    }

    // =====================================
    // Membership Management
    // =====================================

    /**
     * Invite a guild to join the alliance
     *
     * @param int|string $alliance Alliance ID
     * @param int $guildId Guild to invite
     */
    public function invite(int|string $alliance, int $guildId): array
    {
        return $this->client->post($this->path("{$alliance}/invite"), [
            'guild_id' => $guildId,
        ]);
    }

    /**
     * Apply to join an alliance
     *
     * @param int|string $alliance Alliance ID
     * @param int $guildId Guild applying
     * @param string|null $message Application message
     */
    public function apply(int|string $alliance, int $guildId, ?string $message = null): array
    {
        return $this->client->post($this->path("{$alliance}/apply"), [
            'guild_id' => $guildId,
            'message' => $message,
        ]);
    }

    /**
     * Accept a guild's application or invitation
     */
    public function acceptMember(int|string $alliance, int $membershipId): array
    {
        return $this->client->post($this->path("{$alliance}/members/{$membershipId}/accept"));
    }

    /**
     * Reject a guild's application
     */
    public function rejectMember(int|string $alliance, int $membershipId): array
    {
        return $this->client->post($this->path("{$alliance}/members/{$membershipId}/reject"));
    }

    /**
     * Leave an alliance
     *
     * Founder cannot leave - must transfer leadership or dissolve.
     */
    public function leave(int|string $alliance, int $guildId): array
    {
        return $this->client->post($this->path("{$alliance}/leave"), [
            'guild_id' => $guildId,
        ]);
    }

    /**
     * Kick a guild from the alliance
     */
    public function kick(int|string $alliance, int $membershipId): array
    {
        return $this->client->post($this->path("{$alliance}/members/{$membershipId}/kick"));
    }

    /**
     * Promote/demote a guild member
     *
     * @param int|string $alliance Alliance ID
     * @param int $membershipId Membership ID
     * @param string $role New role: 'member', 'officer', or 'leader'
     */
    public function setRole(int|string $alliance, int $membershipId, string $role): array
    {
        return $this->client->post($this->path("{$alliance}/members/{$membershipId}/promote"), [
            'role' => $role,
        ]);
    }

    /**
     * Transfer alliance leadership to another guild
     */
    public function transferLeadership(int|string $alliance, int $guildId): array
    {
        return $this->client->post($this->path("{$alliance}/transfer-leadership"), [
            'guild_id' => $guildId,
        ]);
    }

    // =====================================
    // Treasury
    // =====================================

    /**
     * Get treasury balance and information
     */
    public function treasury(int|string $alliance): array
    {
        return $this->client->get($this->path("{$alliance}/treasury"));
    }

    /**
     * Make a contribution to the treasury
     *
     * @param int|string $alliance Alliance ID
     * @param int $guildId Contributing guild
     * @param float $amount Contribution amount
     * @param string|null $description Optional description
     */
    public function contribute(int|string $alliance, int $guildId, float $amount, ?string $description = null): array
    {
        return $this->client->post($this->path("{$alliance}/treasury/contribute"), [
            'guild_id' => $guildId,
            'amount' => $amount,
            'description' => $description,
        ]);
    }

    /**
     * Propose an expense from the treasury
     *
     * Large expenses require a vote from member guilds.
     */
    public function proposeExpense(int|string $alliance, float $amount, string $description): array
    {
        return $this->client->post($this->path("{$alliance}/treasury/expenses"), [
            'amount' => $amount,
            'description' => $description,
        ]);
    }

    /**
     * Vote on a treasury expense
     *
     * @param int|string $alliance Alliance ID
     * @param int $transactionId Transaction to vote on
     * @param int $guildId Voting guild
     * @param bool $inFavor Vote in favor or against
     */
    public function voteOnExpense(int|string $alliance, int $transactionId, int $guildId, bool $inFavor): array
    {
        return $this->client->post($this->path("{$alliance}/treasury/{$transactionId}/vote"), [
            'guild_id' => $guildId,
            'in_favor' => $inFavor,
        ]);
    }

    /**
     * Get treasury transaction history
     */
    public function transactions(int|string $alliance, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("{$alliance}/treasury/transactions"), $params);
    }

    /**
     * Get pending votes
     */
    public function pendingVotes(int|string $alliance): array
    {
        return $this->client->get($this->path("{$alliance}/treasury/pending-votes"));
    }

    // =====================================
    // Wars
    // =====================================

    /**
     * Declare war on another alliance
     *
     * @param int|string $alliance Your alliance (challenger)
     * @param int $defenderAllianceId Target alliance
     */
    public function declareWar(int|string $alliance, int $defenderAllianceId): array
    {
        return $this->client->post($this->path("{$alliance}/war/declare"), [
            'defender_alliance_id' => $defenderAllianceId,
        ]);
    }

    /**
     * Accept a war declaration
     */
    public function acceptWar(int|string $alliance, int $warId): array
    {
        return $this->client->post($this->path("{$alliance}/war/{$warId}/accept"));
    }

    /**
     * Decline a war declaration
     */
    public function declineWar(int|string $alliance, int $warId): array
    {
        return $this->client->post($this->path("{$alliance}/war/{$warId}/decline"));
    }

    /**
     * Get current war status
     */
    public function warStatus(int|string $alliance): array
    {
        return $this->client->get($this->path("{$alliance}/war"));
    }

    /**
     * Get war history
     */
    public function warHistory(int|string $alliance, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("{$alliance}/war/history"), $params);
    }

    /**
     * Get war leaderboard (contribution rankings)
     */
    public function warLeaderboard(int|string $alliance, int $warId): array
    {
        return $this->client->get($this->path("{$alliance}/war/{$warId}/leaderboard"));
    }
}
