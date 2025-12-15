<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK\Resources;

use Sensei\PartnerSDK\Support\PaginatedResponse;

/**
 * Events resource
 *
 * Manage live events, webinars, and workshops
 */
class Events extends Resource
{
    protected string $basePath = 'v1/partners/events';

    /**
     * List all events
     */
    public function all(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path(), $params);
    }

    /**
     * Get upcoming events
     */
    public function upcoming(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('upcoming'), $params);
    }

    /**
     * Get past events
     */
    public function past(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('past'), $params);
    }

    /**
     * Get a specific event
     */
    public function get(int $id): array
    {
        return $this->find($id);
    }

    /**
     * Create a new event
     */
    public function create(array $data): array
    {
        return $this->store($data);
    }

    /**
     * Update an event
     */
    public function updateEvent(int $id, array $data): array
    {
        return $this->update($id, $data);
    }

    /**
     * Delete an event
     */
    public function delete(int $id): array
    {
        return $this->destroy($id);
    }

    /**
     * Publish event
     */
    public function publish(int $eventId): array
    {
        return $this->client->post($this->path("{$eventId}/publish"));
    }

    /**
     * Unpublish event
     */
    public function unpublish(int $eventId): array
    {
        return $this->client->post($this->path("{$eventId}/unpublish"));
    }

    /**
     * Cancel event
     */
    public function cancel(int $eventId, ?string $reason = null): array
    {
        return $this->client->post($this->path("{$eventId}/cancel"), ['reason' => $reason]);
    }

    /**
     * Duplicate event
     */
    public function duplicate(int $eventId): array
    {
        return $this->client->post($this->path("{$eventId}/duplicate"));
    }

    // === Registrations ===

    /**
     * Get event registrations
     */
    public function registrations(int $eventId, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("{$eventId}/registrations"), $params);
    }

    /**
     * Register user for event
     */
    public function register(int $eventId, int $userId, array $data = []): array
    {
        return $this->client->post($this->path("{$eventId}/register"), array_merge($data, ['user_id' => $userId]));
    }

    /**
     * Unregister user from event
     */
    public function unregister(int $eventId, int $userId): array
    {
        return $this->client->post($this->path("{$eventId}/unregister"), ['user_id' => $userId]);
    }

    /**
     * Check in attendee
     */
    public function checkIn(int $eventId, int $userId): array
    {
        return $this->client->post($this->path("{$eventId}/check-in"), ['user_id' => $userId]);
    }

    /**
     * Get check-in status
     */
    public function checkInStatus(int $eventId): array
    {
        return $this->client->get($this->path("{$eventId}/check-in/status"));
    }

    /**
     * Get waitlist
     */
    public function waitlist(int $eventId): PaginatedResponse
    {
        return $this->paginate($this->path("{$eventId}/waitlist"));
    }

    /**
     * Add to waitlist
     */
    public function addToWaitlist(int $eventId, int $userId): array
    {
        return $this->client->post($this->path("{$eventId}/waitlist"), ['user_id' => $userId]);
    }

    // === Sessions (for multi-session events) ===

    /**
     * List event sessions
     */
    public function sessions(int $eventId): array
    {
        return $this->client->get($this->path("{$eventId}/sessions"));
    }

    /**
     * Create session
     */
    public function createSession(int $eventId, array $data): array
    {
        return $this->client->post($this->path("{$eventId}/sessions"), $data);
    }

    /**
     * Update session
     */
    public function updateSession(int $eventId, int $sessionId, array $data): array
    {
        return $this->client->put($this->path("{$eventId}/sessions/{$sessionId}"), $data);
    }

    /**
     * Delete session
     */
    public function deleteSession(int $eventId, int $sessionId): array
    {
        return $this->client->delete($this->path("{$eventId}/sessions/{$sessionId}"));
    }

    // === Speakers ===

    /**
     * List event speakers
     */
    public function speakers(int $eventId): array
    {
        return $this->client->get($this->path("{$eventId}/speakers"));
    }

    /**
     * Add speaker
     */
    public function addSpeaker(int $eventId, array $data): array
    {
        return $this->client->post($this->path("{$eventId}/speakers"), $data);
    }

    /**
     * Update speaker
     */
    public function updateSpeaker(int $eventId, int $speakerId, array $data): array
    {
        return $this->client->put($this->path("{$eventId}/speakers/{$speakerId}"), $data);
    }

    /**
     * Remove speaker
     */
    public function removeSpeaker(int $eventId, int $speakerId): array
    {
        return $this->client->delete($this->path("{$eventId}/speakers/{$speakerId}"));
    }

    // === Live Streaming ===

    /**
     * Get streaming info
     */
    public function streamingInfo(int $eventId): array
    {
        return $this->client->get($this->path("{$eventId}/streaming"));
    }

    /**
     * Start streaming
     */
    public function startStreaming(int $eventId): array
    {
        return $this->client->post($this->path("{$eventId}/streaming/start"));
    }

    /**
     * Stop streaming
     */
    public function stopStreaming(int $eventId): array
    {
        return $this->client->post($this->path("{$eventId}/streaming/stop"));
    }

    /**
     * Get stream key
     */
    public function streamKey(int $eventId): array
    {
        return $this->client->get($this->path("{$eventId}/streaming/key"));
    }

    /**
     * Regenerate stream key
     */
    public function regenerateStreamKey(int $eventId): array
    {
        return $this->client->post($this->path("{$eventId}/streaming/key/regenerate"));
    }

    // === Recordings ===

    /**
     * Get event recordings
     */
    public function recordings(int $eventId): array
    {
        return $this->client->get($this->path("{$eventId}/recordings"));
    }

    /**
     * Start recording
     */
    public function startRecording(int $eventId): array
    {
        return $this->client->post($this->path("{$eventId}/recording/start"));
    }

    /**
     * Stop recording
     */
    public function stopRecording(int $eventId): array
    {
        return $this->client->post($this->path("{$eventId}/recording/stop"));
    }

    /**
     * Delete recording
     */
    public function deleteRecording(int $eventId, int $recordingId): array
    {
        return $this->client->delete($this->path("{$eventId}/recordings/{$recordingId}"));
    }

    // === Q&A ===

    /**
     * Get Q&A questions
     */
    public function questions(int $eventId, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("{$eventId}/questions"), $params);
    }

    /**
     * Submit question
     */
    public function submitQuestion(int $eventId, int $userId, string $question): array
    {
        return $this->client->post($this->path("{$eventId}/questions"), [
            'user_id' => $userId,
            'question' => $question
        ]);
    }

    /**
     * Answer question
     */
    public function answerQuestion(int $eventId, int $questionId, string $answer): array
    {
        return $this->client->post($this->path("{$eventId}/questions/{$questionId}/answer"), [
            'answer' => $answer
        ]);
    }

    /**
     * Upvote question
     */
    public function upvoteQuestion(int $eventId, int $questionId): array
    {
        return $this->client->post($this->path("{$eventId}/questions/{$questionId}/upvote"));
    }

    /**
     * Mark question as answered
     */
    public function markQuestionAnswered(int $eventId, int $questionId): array
    {
        return $this->client->post($this->path("{$eventId}/questions/{$questionId}/answered"));
    }

    // === Polls ===

    /**
     * Get event polls
     */
    public function polls(int $eventId): array
    {
        return $this->client->get($this->path("{$eventId}/polls"));
    }

    /**
     * Create poll
     */
    public function createPoll(int $eventId, array $data): array
    {
        return $this->client->post($this->path("{$eventId}/polls"), $data);
    }

    /**
     * Start poll
     */
    public function startPoll(int $eventId, int $pollId): array
    {
        return $this->client->post($this->path("{$eventId}/polls/{$pollId}/start"));
    }

    /**
     * End poll
     */
    public function endPoll(int $eventId, int $pollId): array
    {
        return $this->client->post($this->path("{$eventId}/polls/{$pollId}/end"));
    }

    /**
     * Get poll results
     */
    public function pollResults(int $eventId, int $pollId): array
    {
        return $this->client->get($this->path("{$eventId}/polls/{$pollId}/results"));
    }

    /**
     * Vote in poll
     */
    public function vote(int $eventId, int $pollId, int $userId, array $answers): array
    {
        return $this->client->post($this->path("{$eventId}/polls/{$pollId}/vote"), [
            'user_id' => $userId,
            'answers' => $answers
        ]);
    }

    // === Chat ===

    /**
     * Get event chat messages
     */
    public function chatMessages(int $eventId, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("{$eventId}/chat"), $params);
    }

    /**
     * Send chat message
     */
    public function sendChatMessage(int $eventId, int $userId, string $message): array
    {
        return $this->client->post($this->path("{$eventId}/chat"), [
            'user_id' => $userId,
            'message' => $message
        ]);
    }

    /**
     * Enable/disable chat
     */
    public function toggleChat(int $eventId, bool $enabled): array
    {
        return $this->client->post($this->path("{$eventId}/chat/toggle"), ['enabled' => $enabled]);
    }

    // === Feedback ===

    /**
     * Get event feedback
     */
    public function feedback(int $eventId, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("{$eventId}/feedback"), $params);
    }

    /**
     * Submit feedback
     */
    public function submitFeedback(int $eventId, int $userId, array $data): array
    {
        return $this->client->post($this->path("{$eventId}/feedback"), array_merge($data, ['user_id' => $userId]));
    }

    /**
     * Get feedback summary
     */
    public function feedbackSummary(int $eventId): array
    {
        return $this->client->get($this->path("{$eventId}/feedback/summary"));
    }

    // === Media ===

    /**
     * Upload event image
     */
    public function uploadImage(int $eventId, string $filePath): array
    {
        return $this->client->upload($this->path("{$eventId}/images"), $filePath, 'image');
    }

    /**
     * Upload event material
     */
    public function uploadMaterial(int $eventId, string $filePath, array $data = []): array
    {
        return $this->client->upload($this->path("{$eventId}/materials"), $filePath, 'file', $data);
    }

    /**
     * Get event materials
     */
    public function materials(int $eventId): array
    {
        return $this->client->get($this->path("{$eventId}/materials"));
    }

    // === Analytics ===

    /**
     * Get event analytics
     */
    public function analytics(int $eventId): array
    {
        return $this->client->get($this->path("{$eventId}/analytics"));
    }

    /**
     * Get attendance report
     */
    public function attendanceReport(int $eventId): array
    {
        return $this->client->get($this->path("{$eventId}/reports/attendance"));
    }

    /**
     * Get engagement report
     */
    public function engagementReport(int $eventId): array
    {
        return $this->client->get($this->path("{$eventId}/reports/engagement"));
    }

    // === Reminders ===

    /**
     * Send reminder to registrants
     */
    public function sendReminder(int $eventId, array $data = []): array
    {
        return $this->client->post($this->path("{$eventId}/reminders/send"), $data);
    }

    /**
     * Schedule reminder
     */
    public function scheduleReminder(int $eventId, array $data): array
    {
        return $this->client->post($this->path("{$eventId}/reminders/schedule"), $data);
    }

    // === Certificates ===

    /**
     * Generate attendance certificate
     */
    public function generateCertificate(int $eventId, int $userId): array
    {
        return $this->client->post($this->path("{$eventId}/certificates"), ['user_id' => $userId]);
    }

    /**
     * Get event certificates
     */
    public function certificates(int $eventId): array
    {
        return $this->client->get($this->path("{$eventId}/certificates"));
    }
}
