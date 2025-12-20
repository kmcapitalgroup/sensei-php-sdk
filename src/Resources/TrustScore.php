<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK\Resources;

use Sensei\PartnerSDK\Support\PaginatedResponse;

/**
 * Trust Score management resource
 *
 * The Trust Score system allows users to build reputation through:
 * - User-to-user trust reactions (endorsements, recommendations)
 * - Guild trust voting
 * - Alliance trust voting
 *
 * Higher-level users have more weight when voting.
 * Negative votes require proof of interaction.
 */
class TrustScore extends Resource
{
    protected string $basePath = 'v1/{tenant}/community';

    // =====================================
    // User Trust Score
    // =====================================

    /**
     * Give a trust reaction to another user
     *
     * @param array $data Reaction data:
     *   - trustee_uuid: string (required) - Target user's UUID
     *   - trust_score: float (required) - Score from -5 to 5 (negative requires proof)
     *   - reaction_type: string (optional) - 'trust', 'endorsement', or 'recommendation'
     *   - comment: string (optional) - Comment (max 500 chars)
     *   - negative_category: string (required if negative) - Category of issue
     *   - negative_reason: string (required if negative) - Detailed reason
     *   - interaction_type: string (required if negative) - Type of interaction proof
     *   - interaction_id: int (required if negative) - ID of the interaction
     *
     * @return array Response with reaction details and remaining reactions count
     */
    public function giveReaction(array $data): array
    {
        return $this->client->post($this->path('trust-reactions'), $data);
    }

    /**
     * Get trust score breakdown for a user
     *
     * @param string $userUuid User's UUID
     * @return array Detailed breakdown including:
     *   - total_score: Weighted average score
     *   - total_reactions: Number of reactions received
     *   - positive_reactions: Number of positive reactions
     *   - negative_reactions: Number of negative reactions
     *   - breakdown_by_level: Score contributions by voter level
     */
    public function getUserBreakdown(string $userUuid): array
    {
        return $this->client->get("v1/{tenant}/users/{$userUuid}/trust-score");
    }

    /**
     * Get current user's weekly trust reaction stats
     *
     * @return array Stats including:
     *   - reactions_given_this_week: Count of reactions given
     *   - reactions_remaining: Reactions left this week
     *   - max_weekly_reactions: Maximum allowed per week
     *   - week_resets_at: When the counter resets
     *   - recent_reactions_given: List of recent reactions
     */
    public function getMyStats(): array
    {
        return $this->client->get($this->path('my-trust-stats'));
    }

    /**
     * Get trust reactions received by current user
     *
     * @param array $params Query parameters:
     *   - limit: int (default 20, max 100)
     *   - include_negative: bool (default true)
     *
     * @return array Reactions received with voter info
     */
    public function getReactionsReceived(array $params = []): array
    {
        return $this->client->get($this->path('trust-reactions-received'), $params);
    }

    /**
     * Get level weights information
     *
     * Higher level users have more influence on trust scores.
     *
     * @return array Level weights and descriptions
     */
    public function getLevelWeights(): array
    {
        return $this->client->get($this->path('trust-level-weights'));
    }

    /**
     * Check if current user can give negative vote to another user
     *
     * Negative votes require:
     * - Minimum voter level
     * - Proof of recent interaction with the user
     *
     * @param string $userUuid Target user's UUID
     * @return array Eligibility status and requirements
     */
    public function checkNegativeVoteEligibility(string $userUuid): array
    {
        return $this->client->get($this->path("check-negative-vote-eligibility/{$userUuid}"));
    }

    /**
     * Respond to a negative vote received
     *
     * Users can respond once to explain their side.
     *
     * @param int $reactionId The negative reaction ID
     * @param string $response The user's response (max 1000 chars)
     */
    public function respondToNegativeVote(int $reactionId, string $response): array
    {
        return $this->client->post($this->path("trust-reactions/{$reactionId}/respond"), [
            'response' => $response,
        ]);
    }

    /**
     * Check if user can respond to a specific negative vote
     *
     * @param int $reactionId The reaction ID
     * @return array Whether response is allowed and why
     */
    public function canRespondToVote(int $reactionId): array
    {
        return $this->client->get($this->path("trust-reactions/{$reactionId}/can-respond"));
    }

    /**
     * Get all interactions between current user and another user
     *
     * Used to determine eligibility for negative votes.
     *
     * @param string $userUuid The other user's UUID
     * @return array List of interactions within the allowed window
     */
    public function getInteractionsWith(string $userUuid): array
    {
        return $this->client->get($this->path("my-interactions/{$userUuid}"));
    }

    // =====================================
    // Guild Trust Score
    // =====================================

    /**
     * Get trust score for a guild
     *
     * @param int $guildId Guild ID
     * @return array Trust score breakdown
     */
    public function getGuildTrustScore(int $guildId): array
    {
        return $this->client->get("v1/{tenant}/guilds/{$guildId}/trust");
    }

    /**
     * Vote on a guild's trustworthiness
     *
     * @param int $guildId Guild ID
     * @param array $data Vote data:
     *   - reaction: string (required) - 'positive' or 'negative'
     *   - negative_reason: string (required if negative) - Reason for negative vote
     *   - proof_of_interaction: string (optional) - Evidence of interaction
     */
    public function voteOnGuild(int $guildId, array $data): array
    {
        return $this->client->post("v1/{tenant}/guilds/{$guildId}/trust/vote", $data);
    }

    /**
     * Get current user's vote on a guild
     *
     * @param int $guildId Guild ID
     * @return array User's existing vote or null
     */
    public function getMyGuildVote(int $guildId): array
    {
        return $this->client->get("v1/{tenant}/guilds/{$guildId}/trust/my-vote");
    }

    /**
     * Get all votes for a guild
     *
     * @param int $guildId Guild ID
     * @param array $params Pagination parameters
     */
    public function getGuildVotes(int $guildId, array $params = []): PaginatedResponse
    {
        return $this->paginate("v1/{tenant}/guilds/{$guildId}/trust/votes", $params);
    }

    // =====================================
    // Alliance Trust Score
    // =====================================

    /**
     * Get trust score for an alliance
     *
     * @param int|string $allianceId Alliance ID or slug
     * @return array Trust score breakdown
     */
    public function getAllianceTrustScore(int|string $allianceId): array
    {
        return $this->client->get("v1/{tenant}/alliances/{$allianceId}/trust");
    }

    /**
     * Vote on an alliance's trustworthiness
     *
     * @param int|string $allianceId Alliance ID or slug
     * @param array $data Vote data:
     *   - reaction: string (required) - 'positive' or 'negative'
     *   - negative_reason: string (required if negative) - Reason for negative vote
     *   - proof_of_interaction: string (optional) - Evidence of interaction
     */
    public function voteOnAlliance(int|string $allianceId, array $data): array
    {
        return $this->client->post("v1/{tenant}/alliances/{$allianceId}/trust/vote", $data);
    }

    /**
     * Get current user's vote on an alliance
     *
     * @param int|string $allianceId Alliance ID or slug
     * @return array User's existing vote or null
     */
    public function getMyAllianceVote(int|string $allianceId): array
    {
        return $this->client->get("v1/{tenant}/alliances/{$allianceId}/trust/my-vote");
    }

    /**
     * Get all votes for an alliance
     *
     * @param int|string $allianceId Alliance ID or slug
     * @param array $params Pagination parameters
     */
    public function getAllianceVotes(int|string $allianceId, array $params = []): PaginatedResponse
    {
        return $this->paginate("v1/{tenant}/alliances/{$allianceId}/trust/votes", $params);
    }
}
