<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK\Resources;

use Sensei\PartnerSDK\Support\PaginatedResponse;

/**
 * Gamification resource
 *
 * Manage XP, levels, badges, achievements, and rewards
 */
class Gamification extends Resource
{
    protected string $basePath = 'partner/gamification';

    // === XP & Levels ===

    /**
     * Get XP configuration
     */
    public function xpConfig(): array
    {
        return $this->client->get($this->path('xp/config'));
    }

    /**
     * Update XP configuration
     */
    public function updateXpConfig(array $config): array
    {
        return $this->client->put($this->path('xp/config'), $config);
    }

    /**
     * Get level thresholds
     */
    public function levels(): array
    {
        return $this->client->get($this->path('levels'));
    }

    /**
     * Update level thresholds
     */
    public function updateLevels(array $levels): array
    {
        return $this->client->put($this->path('levels'), ['levels' => $levels]);
    }

    /**
     * Award XP to user
     */
    public function awardXp(int $userId, int $amount, string $reason, ?string $source = null): array
    {
        return $this->client->post($this->path('xp/award'), [
            'user_id' => $userId,
            'amount' => $amount,
            'reason' => $reason,
            'source' => $source
        ]);
    }

    /**
     * Deduct XP from user
     */
    public function deductXp(int $userId, int $amount, string $reason): array
    {
        return $this->client->post($this->path('xp/deduct'), [
            'user_id' => $userId,
            'amount' => $amount,
            'reason' => $reason
        ]);
    }

    /**
     * Get user XP history
     */
    public function userXpHistory(int $userId, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("users/{$userId}/xp-history"), $params);
    }

    /**
     * Get XP leaderboard
     */
    public function xpLeaderboard(array $params = []): array
    {
        return $this->client->get($this->path('leaderboard/xp'), $params);
    }

    // === Badges ===

    /**
     * List all badges
     */
    public function badges(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('badges'), $params);
    }

    /**
     * Get a badge
     */
    public function badge(int $badgeId): array
    {
        return $this->client->get($this->path("badges/{$badgeId}"));
    }

    /**
     * Create badge
     */
    public function createBadge(array $data): array
    {
        return $this->client->post($this->path('badges'), $data);
    }

    /**
     * Update badge
     */
    public function updateBadge(int $badgeId, array $data): array
    {
        return $this->client->put($this->path("badges/{$badgeId}"), $data);
    }

    /**
     * Delete badge
     */
    public function deleteBadge(int $badgeId): array
    {
        return $this->client->delete($this->path("badges/{$badgeId}"));
    }

    /**
     * Upload badge icon
     */
    public function uploadBadgeIcon(int $badgeId, string $filePath): array
    {
        return $this->client->upload($this->path("badges/{$badgeId}/icon"), $filePath, 'icon');
    }

    /**
     * Award badge to user
     */
    public function awardBadge(int $userId, int $badgeId, ?string $reason = null): array
    {
        return $this->client->post($this->path("badges/{$badgeId}/award"), [
            'user_id' => $userId,
            'reason' => $reason
        ]);
    }

    /**
     * Revoke badge from user
     */
    public function revokeBadge(int $userId, int $badgeId): array
    {
        return $this->client->post($this->path("badges/{$badgeId}/revoke"), [
            'user_id' => $userId
        ]);
    }

    /**
     * Get users with badge
     */
    public function badgeHolders(int $badgeId, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("badges/{$badgeId}/users"), $params);
    }

    /**
     * Get user badges
     */
    public function userBadges(int $userId): array
    {
        return $this->client->get($this->path("users/{$userId}/badges"));
    }

    // === Achievements ===

    /**
     * List achievements
     */
    public function achievements(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('achievements'), $params);
    }

    /**
     * Get achievement
     */
    public function achievement(int $achievementId): array
    {
        return $this->client->get($this->path("achievements/{$achievementId}"));
    }

    /**
     * Create achievement
     */
    public function createAchievement(array $data): array
    {
        return $this->client->post($this->path('achievements'), $data);
    }

    /**
     * Update achievement
     */
    public function updateAchievement(int $achievementId, array $data): array
    {
        return $this->client->put($this->path("achievements/{$achievementId}"), $data);
    }

    /**
     * Delete achievement
     */
    public function deleteAchievement(int $achievementId): array
    {
        return $this->client->delete($this->path("achievements/{$achievementId}"));
    }

    /**
     * Get user achievements
     */
    public function userAchievements(int $userId): array
    {
        return $this->client->get($this->path("users/{$userId}/achievements"));
    }

    /**
     * Get user achievement progress
     */
    public function userAchievementProgress(int $userId, int $achievementId): array
    {
        return $this->client->get($this->path("users/{$userId}/achievements/{$achievementId}/progress"));
    }

    /**
     * Update achievement progress
     */
    public function updateAchievementProgress(int $userId, int $achievementId, int $progress): array
    {
        return $this->client->post($this->path("users/{$userId}/achievements/{$achievementId}/progress"), [
            'progress' => $progress
        ]);
    }

    // === Challenges ===

    /**
     * List challenges
     */
    public function challenges(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('challenges'), $params);
    }

    /**
     * Get challenge
     */
    public function challenge(int $challengeId): array
    {
        return $this->client->get($this->path("challenges/{$challengeId}"));
    }

    /**
     * Create challenge
     */
    public function createChallenge(array $data): array
    {
        return $this->client->post($this->path('challenges'), $data);
    }

    /**
     * Update challenge
     */
    public function updateChallenge(int $challengeId, array $data): array
    {
        return $this->client->put($this->path("challenges/{$challengeId}"), $data);
    }

    /**
     * Delete challenge
     */
    public function deleteChallenge(int $challengeId): array
    {
        return $this->client->delete($this->path("challenges/{$challengeId}"));
    }

    /**
     * Get challenge participants
     */
    public function challengeParticipants(int $challengeId, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("challenges/{$challengeId}/participants"), $params);
    }

    /**
     * Join challenge
     */
    public function joinChallenge(int $challengeId, int $userId): array
    {
        return $this->client->post($this->path("challenges/{$challengeId}/join"), ['user_id' => $userId]);
    }

    /**
     * Leave challenge
     */
    public function leaveChallenge(int $challengeId, int $userId): array
    {
        return $this->client->post($this->path("challenges/{$challengeId}/leave"), ['user_id' => $userId]);
    }

    /**
     * Get challenge leaderboard
     */
    public function challengeLeaderboard(int $challengeId): array
    {
        return $this->client->get($this->path("challenges/{$challengeId}/leaderboard"));
    }

    /**
     * Complete challenge for user
     */
    public function completeChallenge(int $challengeId, int $userId): array
    {
        return $this->client->post($this->path("challenges/{$challengeId}/complete"), ['user_id' => $userId]);
    }

    // === Quests ===

    /**
     * List quests
     */
    public function quests(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('quests'), $params);
    }

    /**
     * Get quest
     */
    public function quest(int $questId): array
    {
        return $this->client->get($this->path("quests/{$questId}"));
    }

    /**
     * Create quest
     */
    public function createQuest(array $data): array
    {
        return $this->client->post($this->path('quests'), $data);
    }

    /**
     * Update quest
     */
    public function updateQuest(int $questId, array $data): array
    {
        return $this->client->put($this->path("quests/{$questId}"), $data);
    }

    /**
     * Delete quest
     */
    public function deleteQuest(int $questId): array
    {
        return $this->client->delete($this->path("quests/{$questId}"));
    }

    /**
     * Get user quest progress
     */
    public function userQuestProgress(int $userId, int $questId): array
    {
        return $this->client->get($this->path("users/{$userId}/quests/{$questId}"));
    }

    /**
     * Get active daily quests
     */
    public function dailyQuests(): array
    {
        return $this->client->get($this->path('quests/daily'));
    }

    /**
     * Get active weekly quests
     */
    public function weeklyQuests(): array
    {
        return $this->client->get($this->path('quests/weekly'));
    }

    // === Rewards ===

    /**
     * List rewards
     */
    public function rewards(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('rewards'), $params);
    }

    /**
     * Get reward
     */
    public function reward(int $rewardId): array
    {
        return $this->client->get($this->path("rewards/{$rewardId}"));
    }

    /**
     * Create reward
     */
    public function createReward(array $data): array
    {
        return $this->client->post($this->path('rewards'), $data);
    }

    /**
     * Update reward
     */
    public function updateReward(int $rewardId, array $data): array
    {
        return $this->client->put($this->path("rewards/{$rewardId}"), $data);
    }

    /**
     * Delete reward
     */
    public function deleteReward(int $rewardId): array
    {
        return $this->client->delete($this->path("rewards/{$rewardId}"));
    }

    /**
     * Grant reward to user
     */
    public function grantReward(int $userId, int $rewardId): array
    {
        return $this->client->post($this->path("rewards/{$rewardId}/grant"), ['user_id' => $userId]);
    }

    /**
     * Claim reward
     */
    public function claimReward(int $userId, int $rewardId): array
    {
        return $this->client->post($this->path("users/{$userId}/rewards/{$rewardId}/claim"));
    }

    /**
     * Get user rewards
     */
    public function userRewards(int $userId): array
    {
        return $this->client->get($this->path("users/{$userId}/rewards"));
    }

    // === Streaks ===

    /**
     * Get user streak
     */
    public function userStreak(int $userId): array
    {
        return $this->client->get($this->path("users/{$userId}/streak"));
    }

    /**
     * Get streak configuration
     */
    public function streakConfig(): array
    {
        return $this->client->get($this->path('streak/config'));
    }

    /**
     * Update streak configuration
     */
    public function updateStreakConfig(array $config): array
    {
        return $this->client->put($this->path('streak/config'), $config);
    }

    /**
     * Get streak leaderboard
     */
    public function streakLeaderboard(array $params = []): array
    {
        return $this->client->get($this->path('leaderboard/streak'), $params);
    }

    // === Points/Coins ===

    /**
     * Get coins configuration
     */
    public function coinsConfig(): array
    {
        return $this->client->get($this->path('coins/config'));
    }

    /**
     * Update coins configuration
     */
    public function updateCoinsConfig(array $config): array
    {
        return $this->client->put($this->path('coins/config'), $config);
    }

    /**
     * Award coins to user
     */
    public function awardCoins(int $userId, int $amount, string $reason): array
    {
        return $this->client->post($this->path('coins/award'), [
            'user_id' => $userId,
            'amount' => $amount,
            'reason' => $reason
        ]);
    }

    /**
     * Deduct coins from user
     */
    public function deductCoins(int $userId, int $amount, string $reason): array
    {
        return $this->client->post($this->path('coins/deduct'), [
            'user_id' => $userId,
            'amount' => $amount,
            'reason' => $reason
        ]);
    }

    /**
     * Get user coins balance
     */
    public function userCoins(int $userId): array
    {
        return $this->client->get($this->path("users/{$userId}/coins"));
    }

    /**
     * Get user coins history
     */
    public function userCoinsHistory(int $userId, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("users/{$userId}/coins/history"), $params);
    }

    // === Statistics ===

    /**
     * Get gamification statistics
     */
    public function statistics(array $params = []): array
    {
        return $this->client->get($this->path('statistics'), $params);
    }

    /**
     * Get user gamification profile
     */
    public function userProfile(int $userId): array
    {
        return $this->client->get($this->path("users/{$userId}/profile"));
    }
}
