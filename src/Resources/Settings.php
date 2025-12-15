<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK\Resources;

/**
 * Partner settings management resource
 *
 * Manage account settings, preferences, and configurations
 */
class Settings extends Resource
{
    protected string $basePath = 'v1/partners/settings';

    /**
     * Get all settings
     */
    public function all(): array
    {
        return $this->client->get($this->path());
    }

    /**
     * Get a specific setting
     */
    public function get(string $key): array
    {
        return $this->client->get($this->path($key));
    }

    /**
     * Update a setting
     */
    public function set(string $key, mixed $value): array
    {
        return $this->client->put($this->path($key), ['value' => $value]);
    }

    /**
     * Update multiple settings at once
     */
    public function updateBulk(array $settings): array
    {
        return $this->client->put($this->path(), ['settings' => $settings]);
    }

    /**
     * Reset a setting to default
     */
    public function reset(string $key): array
    {
        return $this->client->delete($this->path($key));
    }

    /**
     * Reset all settings to defaults
     */
    public function resetAll(): array
    {
        return $this->client->post($this->path('reset-all'));
    }

    // === Notification Settings ===

    /**
     * Get notification preferences
     */
    public function notifications(): array
    {
        return $this->client->get($this->path('notifications'));
    }

    /**
     * Update notification preferences
     */
    public function updateNotifications(array $preferences): array
    {
        return $this->client->put($this->path('notifications'), $preferences);
    }

    /**
     * Get email notification settings
     */
    public function emailNotifications(): array
    {
        return $this->client->get($this->path('notifications/email'));
    }

    /**
     * Update email notification settings
     */
    public function updateEmailNotifications(array $settings): array
    {
        return $this->client->put($this->path('notifications/email'), $settings);
    }

    // === Security Settings ===

    /**
     * Get security settings
     */
    public function security(): array
    {
        return $this->client->get($this->path('security'));
    }

    /**
     * Update security settings
     */
    public function updateSecurity(array $settings): array
    {
        return $this->client->put($this->path('security'), $settings);
    }

    /**
     * Enable two-factor authentication
     */
    public function enable2fa(): array
    {
        return $this->client->post($this->path('security/2fa/enable'));
    }

    /**
     * Disable two-factor authentication
     */
    public function disable2fa(string $code): array
    {
        return $this->client->post($this->path('security/2fa/disable'), ['code' => $code]);
    }

    /**
     * Verify 2FA setup
     */
    public function verify2fa(string $code): array
    {
        return $this->client->post($this->path('security/2fa/verify'), ['code' => $code]);
    }

    /**
     * Get backup codes
     */
    public function getBackupCodes(): array
    {
        return $this->client->get($this->path('security/backup-codes'));
    }

    /**
     * Regenerate backup codes
     */
    public function regenerateBackupCodes(): array
    {
        return $this->client->post($this->path('security/backup-codes/regenerate'));
    }

    /**
     * Get active sessions
     */
    public function sessions(): array
    {
        return $this->client->get($this->path('security/sessions'));
    }

    /**
     * Revoke a session
     */
    public function revokeSession(string $sessionId): array
    {
        return $this->client->delete($this->path("security/sessions/{$sessionId}"));
    }

    /**
     * Revoke all sessions except current
     */
    public function revokeAllSessions(): array
    {
        return $this->client->post($this->path('security/sessions/revoke-all'));
    }

    // === Payment Settings ===

    /**
     * Get payment settings
     */
    public function payment(): array
    {
        return $this->client->get($this->path('payment'));
    }

    /**
     * Update payment settings
     */
    public function updatePayment(array $settings): array
    {
        return $this->client->put($this->path('payment'), $settings);
    }

    /**
     * Get payout settings
     */
    public function payout(): array
    {
        return $this->client->get($this->path('payout'));
    }

    /**
     * Update payout settings
     */
    public function updatePayout(array $settings): array
    {
        return $this->client->put($this->path('payout'), $settings);
    }

    // === Currency & Localization ===

    /**
     * Get currency settings
     */
    public function currency(): array
    {
        return $this->client->get($this->path('currency'));
    }

    /**
     * Update currency settings
     */
    public function updateCurrency(array $settings): array
    {
        return $this->client->put($this->path('currency'), $settings);
    }

    /**
     * Get supported currencies
     */
    public function supportedCurrencies(): array
    {
        return $this->client->get($this->path('currency/supported'));
    }

    /**
     * Get localization settings
     */
    public function localization(): array
    {
        return $this->client->get($this->path('localization'));
    }

    /**
     * Update localization settings
     */
    public function updateLocalization(array $settings): array
    {
        return $this->client->put($this->path('localization'), $settings);
    }

    // === Email Settings ===

    /**
     * Get email settings
     */
    public function email(): array
    {
        return $this->client->get($this->path('email'));
    }

    /**
     * Update email settings
     */
    public function updateEmail(array $settings): array
    {
        return $this->client->put($this->path('email'), $settings);
    }

    /**
     * Get email templates
     */
    public function emailTemplates(): array
    {
        return $this->client->get($this->path('email/templates'));
    }

    /**
     * Update an email template
     */
    public function updateEmailTemplate(string $templateId, array $content): array
    {
        return $this->client->put($this->path("email/templates/{$templateId}"), $content);
    }

    /**
     * Preview an email template
     */
    public function previewEmailTemplate(string $templateId, array $variables = []): array
    {
        return $this->client->post($this->path("email/templates/{$templateId}/preview"), $variables);
    }

    /**
     * Send test email
     */
    public function sendTestEmail(string $templateId, string $email): array
    {
        return $this->client->post($this->path("email/templates/{$templateId}/test"), ['email' => $email]);
    }

    // === Integration Settings ===

    /**
     * Get integration settings
     */
    public function integrations(): array
    {
        return $this->client->get($this->path('integrations'));
    }

    /**
     * Update integration settings
     */
    public function updateIntegration(string $integration, array $settings): array
    {
        return $this->client->put($this->path("integrations/{$integration}"), $settings);
    }

    /**
     * Test integration connection
     */
    public function testIntegration(string $integration): array
    {
        return $this->client->post($this->path("integrations/{$integration}/test"));
    }
}
