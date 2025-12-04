<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK\Resources;

use Sensei\PartnerSDK\Support\PaginatedResponse;

/**
 * Notifications resource
 *
 * Manage notifications, push notifications, and communication
 */
class Notifications extends Resource
{
    protected string $basePath = 'partner/notifications';

    // === Send Notifications ===

    /**
     * Send notification to user
     */
    public function sendToUser(int $userId, array $data): array
    {
        return $this->client->post($this->path('send'), array_merge($data, ['user_id' => $userId]));
    }

    /**
     * Send notification to multiple users
     */
    public function sendToUsers(array $userIds, array $data): array
    {
        return $this->client->post($this->path('send-bulk'), array_merge($data, ['user_ids' => $userIds]));
    }

    /**
     * Send notification to segment
     */
    public function sendToSegment(int $segmentId, array $data): array
    {
        return $this->client->post($this->path('send-segment'), array_merge($data, ['segment_id' => $segmentId]));
    }

    /**
     * Send notification to all users
     */
    public function broadcast(array $data): array
    {
        return $this->client->post($this->path('broadcast'), $data);
    }

    /**
     * Send notification to guild members
     */
    public function sendToGuild(int $guildId, array $data): array
    {
        return $this->client->post($this->path('send-guild'), array_merge($data, ['guild_id' => $guildId]));
    }

    // === Notification History ===

    /**
     * List sent notifications
     */
    public function all(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path(), $params);
    }

    /**
     * Get a notification
     */
    public function get(int $notificationId): array
    {
        return $this->client->get($this->path($notificationId));
    }

    /**
     * Get notification delivery stats
     */
    public function deliveryStats(int $notificationId): array
    {
        return $this->client->get($this->path("{$notificationId}/stats"));
    }

    // === Templates ===

    /**
     * List notification templates
     */
    public function templates(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('templates'), $params);
    }

    /**
     * Get a template
     */
    public function template(int $templateId): array
    {
        return $this->client->get($this->path("templates/{$templateId}"));
    }

    /**
     * Create template
     */
    public function createTemplate(array $data): array
    {
        return $this->client->post($this->path('templates'), $data);
    }

    /**
     * Update template
     */
    public function updateTemplate(int $templateId, array $data): array
    {
        return $this->client->put($this->path("templates/{$templateId}"), $data);
    }

    /**
     * Delete template
     */
    public function deleteTemplate(int $templateId): array
    {
        return $this->client->delete($this->path("templates/{$templateId}"));
    }

    /**
     * Preview template
     */
    public function previewTemplate(int $templateId, array $variables = []): array
    {
        return $this->client->post($this->path("templates/{$templateId}/preview"), $variables);
    }

    // === Push Notifications ===

    /**
     * Send push notification
     */
    public function sendPush(int $userId, array $data): array
    {
        return $this->client->post($this->path('push/send'), array_merge($data, ['user_id' => $userId]));
    }

    /**
     * Send push to topic
     */
    public function sendPushToTopic(string $topic, array $data): array
    {
        return $this->client->post($this->path('push/topic'), array_merge($data, ['topic' => $topic]));
    }

    /**
     * Register device token
     */
    public function registerDevice(int $userId, string $token, string $platform): array
    {
        return $this->client->post($this->path('push/devices'), [
            'user_id' => $userId,
            'token' => $token,
            'platform' => $platform // ios, android, web
        ]);
    }

    /**
     * Unregister device
     */
    public function unregisterDevice(string $token): array
    {
        return $this->client->delete($this->path("push/devices/{$token}"));
    }

    /**
     * Get user devices
     */
    public function userDevices(int $userId): array
    {
        return $this->client->get($this->path("push/devices/user/{$userId}"));
    }

    // === Email Notifications ===

    /**
     * Send email notification
     */
    public function sendEmail(int $userId, array $data): array
    {
        return $this->client->post($this->path('email/send'), array_merge($data, ['user_id' => $userId]));
    }

    /**
     * Send email to multiple users
     */
    public function sendEmailBulk(array $userIds, array $data): array
    {
        return $this->client->post($this->path('email/send-bulk'), array_merge($data, ['user_ids' => $userIds]));
    }

    /**
     * Get email templates
     */
    public function emailTemplates(): array
    {
        return $this->client->get($this->path('email/templates'));
    }

    /**
     * Update email template
     */
    public function updateEmailTemplate(string $templateName, array $data): array
    {
        return $this->client->put($this->path("email/templates/{$templateName}"), $data);
    }

    /**
     * Send test email
     */
    public function sendTestEmail(string $templateName, string $email): array
    {
        return $this->client->post($this->path("email/templates/{$templateName}/test"), ['email' => $email]);
    }

    // === SMS Notifications ===

    /**
     * Send SMS
     */
    public function sendSms(int $userId, string $message): array
    {
        return $this->client->post($this->path('sms/send'), [
            'user_id' => $userId,
            'message' => $message
        ]);
    }

    /**
     * Send SMS to phone number
     */
    public function sendSmsToPhone(string $phone, string $message): array
    {
        return $this->client->post($this->path('sms/send-phone'), [
            'phone' => $phone,
            'message' => $message
        ]);
    }

    // === Scheduled Notifications ===

    /**
     * Schedule notification
     */
    public function schedule(array $data): array
    {
        return $this->client->post($this->path('schedule'), $data);
    }

    /**
     * Get scheduled notifications
     */
    public function scheduled(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('scheduled'), $params);
    }

    /**
     * Cancel scheduled notification
     */
    public function cancelScheduled(int $scheduleId): array
    {
        return $this->client->delete($this->path("scheduled/{$scheduleId}"));
    }

    /**
     * Update scheduled notification
     */
    public function updateScheduled(int $scheduleId, array $data): array
    {
        return $this->client->put($this->path("scheduled/{$scheduleId}"), $data);
    }

    // === Automation ===

    /**
     * List automation rules
     */
    public function automationRules(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('automation'), $params);
    }

    /**
     * Get automation rule
     */
    public function automationRule(int $ruleId): array
    {
        return $this->client->get($this->path("automation/{$ruleId}"));
    }

    /**
     * Create automation rule
     */
    public function createAutomationRule(array $data): array
    {
        return $this->client->post($this->path('automation'), $data);
    }

    /**
     * Update automation rule
     */
    public function updateAutomationRule(int $ruleId, array $data): array
    {
        return $this->client->put($this->path("automation/{$ruleId}"), $data);
    }

    /**
     * Delete automation rule
     */
    public function deleteAutomationRule(int $ruleId): array
    {
        return $this->client->delete($this->path("automation/{$ruleId}"));
    }

    /**
     * Enable automation rule
     */
    public function enableAutomationRule(int $ruleId): array
    {
        return $this->client->post($this->path("automation/{$ruleId}/enable"));
    }

    /**
     * Disable automation rule
     */
    public function disableAutomationRule(int $ruleId): array
    {
        return $this->client->post($this->path("automation/{$ruleId}/disable"));
    }

    // === Preferences ===

    /**
     * Get user notification preferences
     */
    public function userPreferences(int $userId): array
    {
        return $this->client->get($this->path("preferences/{$userId}"));
    }

    /**
     * Update user notification preferences
     */
    public function updateUserPreferences(int $userId, array $preferences): array
    {
        return $this->client->put($this->path("preferences/{$userId}"), $preferences);
    }

    // === Statistics ===

    /**
     * Get notification statistics
     */
    public function statistics(array $params = []): array
    {
        return $this->client->get($this->path('statistics'), $params);
    }

    /**
     * Get delivery report
     */
    public function deliveryReport(string $startDate, string $endDate): array
    {
        return $this->client->get($this->path('reports/delivery'), [
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
    }
}
