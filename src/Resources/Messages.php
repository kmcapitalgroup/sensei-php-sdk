<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK\Resources;

use Sensei\PartnerSDK\Support\PaginatedResponse;

/**
 * Messaging resource
 *
 * Manage direct messages, conversations, and channel messages
 */
class Messages extends Resource
{
    protected string $basePath = 'v1/partners/messages';

    // === Conversations (DMs) ===

    /**
     * List conversations
     */
    public function conversations(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('conversations'), $params);
    }

    /**
     * Get a conversation
     */
    public function conversation(int $conversationId): array
    {
        return $this->client->get($this->path("conversations/{$conversationId}"));
    }

    /**
     * Start a new conversation
     */
    public function startConversation(array $participantIds, ?string $initialMessage = null): array
    {
        return $this->client->post($this->path('conversations'), [
            'participant_ids' => $participantIds,
            'message' => $initialMessage
        ]);
    }

    /**
     * Get conversation messages
     */
    public function conversationMessages(int $conversationId, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("conversations/{$conversationId}/messages"), $params);
    }

    /**
     * Send message in conversation
     */
    public function sendInConversation(int $conversationId, array $data): array
    {
        return $this->client->post($this->path("conversations/{$conversationId}/messages"), $data);
    }

    /**
     * Mark conversation as read
     */
    public function markConversationRead(int $conversationId): array
    {
        return $this->client->post($this->path("conversations/{$conversationId}/read"));
    }

    /**
     * Leave conversation
     */
    public function leaveConversation(int $conversationId): array
    {
        return $this->client->post($this->path("conversations/{$conversationId}/leave"));
    }

    /**
     * Add participants to conversation
     */
    public function addParticipants(int $conversationId, array $userIds): array
    {
        return $this->client->post($this->path("conversations/{$conversationId}/participants"), [
            'user_ids' => $userIds
        ]);
    }

    /**
     * Remove participant from conversation
     */
    public function removeParticipant(int $conversationId, int $userId): array
    {
        return $this->client->delete($this->path("conversations/{$conversationId}/participants/{$userId}"));
    }

    // === Channel Messages (Guilds) ===

    /**
     * Get channel messages
     */
    public function channelMessages(int $guildId, int $channelId, array $params = []): PaginatedResponse
    {
        return $this->paginate("partner/guilds/{$guildId}/channels/{$channelId}/messages", $params);
    }

    /**
     * Send message in channel
     */
    public function sendInChannel(int $guildId, int $channelId, array $data): array
    {
        return $this->client->post("partner/guilds/{$guildId}/channels/{$channelId}/messages", $data);
    }

    /**
     * Get a specific message
     */
    public function get(int $messageId): array
    {
        return $this->client->get($this->path($messageId));
    }

    /**
     * Edit a message
     */
    public function edit(int $messageId, string $content): array
    {
        return $this->client->put($this->path($messageId), ['content' => $content]);
    }

    /**
     * Delete a message
     */
    public function delete(int $messageId): array
    {
        return $this->client->delete($this->path($messageId));
    }

    /**
     * Pin a message
     */
    public function pin(int $messageId): array
    {
        return $this->client->post($this->path("{$messageId}/pin"));
    }

    /**
     * Unpin a message
     */
    public function unpin(int $messageId): array
    {
        return $this->client->post($this->path("{$messageId}/unpin"));
    }

    // === Reactions ===

    /**
     * Add reaction to message
     */
    public function addReaction(int $messageId, string $emoji): array
    {
        return $this->client->post($this->path("{$messageId}/reactions"), ['emoji' => $emoji]);
    }

    /**
     * Remove reaction from message
     */
    public function removeReaction(int $messageId, string $emoji): array
    {
        return $this->client->delete($this->path("{$messageId}/reactions/{$emoji}"));
    }

    /**
     * Get message reactions
     */
    public function reactions(int $messageId): array
    {
        return $this->client->get($this->path("{$messageId}/reactions"));
    }

    // === Attachments ===

    /**
     * Upload attachment
     */
    public function uploadAttachment(string $filePath, array $data = []): array
    {
        return $this->client->upload($this->path('attachments'), $filePath, 'file', $data);
    }

    /**
     * Delete attachment
     */
    public function deleteAttachment(int $attachmentId): array
    {
        return $this->client->delete($this->path("attachments/{$attachmentId}"));
    }

    // === Threads ===

    /**
     * Create thread from message
     */
    public function createThread(int $messageId, array $data): array
    {
        return $this->client->post($this->path("{$messageId}/threads"), $data);
    }

    /**
     * Get thread messages
     */
    public function threadMessages(int $threadId, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("threads/{$threadId}/messages"), $params);
    }

    /**
     * Send message in thread
     */
    public function sendInThread(int $threadId, array $data): array
    {
        return $this->client->post($this->path("threads/{$threadId}/messages"), $data);
    }

    // === Moderation ===

    /**
     * Report message
     */
    public function report(int $messageId, string $reason): array
    {
        return $this->client->post($this->path("{$messageId}/report"), ['reason' => $reason]);
    }

    /**
     * Get reported messages
     */
    public function reported(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('reported'), $params);
    }

    /**
     * Review reported message
     */
    public function reviewReport(int $reportId, string $action, ?string $note = null): array
    {
        return $this->client->post($this->path("reports/{$reportId}/review"), [
            'action' => $action, // approve, dismiss, warn, ban
            'note' => $note
        ]);
    }

    // === Search ===

    /**
     * Search messages
     */
    public function search(string $query, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('search'), array_merge($params, ['q' => $query]));
    }

    // === Announcements ===

    /**
     * Send announcement to guild
     */
    public function sendAnnouncement(int $guildId, array $data): array
    {
        return $this->client->post("partner/guilds/{$guildId}/announcements", $data);
    }

    /**
     * Get guild announcements
     */
    public function announcements(int $guildId, array $params = []): PaginatedResponse
    {
        return $this->paginate("partner/guilds/{$guildId}/announcements", $params);
    }

    // === Statistics ===

    /**
     * Get messaging statistics
     */
    public function statistics(array $params = []): array
    {
        return $this->client->get($this->path('statistics'), $params);
    }

    /**
     * Get unread count
     */
    public function unreadCount(): array
    {
        return $this->client->get($this->path('unread-count'));
    }
}
