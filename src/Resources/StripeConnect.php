<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK\Resources;

/**
 * Stripe Connect integration resource
 *
 * Manage Stripe Connect account and payments
 */
class StripeConnect extends Resource
{
    protected string $basePath = 'partner/stripe-connect';

    /**
     * Get Stripe Connect account status
     */
    public function status(): array
    {
        return $this->client->get($this->path('status'));
    }

    /**
     * Get onboarding URL
     */
    public function onboardingUrl(string $returnUrl, string $refreshUrl): array
    {
        return $this->client->post($this->path('onboarding'), [
            'return_url' => $returnUrl,
            'refresh_url' => $refreshUrl,
        ]);
    }

    /**
     * Complete onboarding
     */
    public function completeOnboarding(): array
    {
        return $this->client->post($this->path('onboarding/complete'));
    }

    /**
     * Get account link for dashboard access
     */
    public function dashboardLink(): array
    {
        return $this->client->get($this->path('dashboard-link'));
    }

    /**
     * Get login link (Express dashboard)
     */
    public function loginLink(): array
    {
        return $this->client->get($this->path('login-link'));
    }

    /**
     * Get account details
     */
    public function account(): array
    {
        return $this->client->get($this->path('account'));
    }

    /**
     * Update account details
     */
    public function updateAccount(array $data): array
    {
        return $this->client->put($this->path('account'), $data);
    }

    /**
     * Get capabilities status
     */
    public function capabilities(): array
    {
        return $this->client->get($this->path('capabilities'));
    }

    /**
     * Get verification requirements
     */
    public function verificationRequirements(): array
    {
        return $this->client->get($this->path('verification'));
    }

    /**
     * Upload verification document
     */
    public function uploadDocument(string $filePath, string $purpose): array
    {
        return $this->client->upload($this->path('documents'), $filePath, 'file', [
            'purpose' => $purpose,
        ]);
    }

    /**
     * Get bank accounts
     */
    public function bankAccounts(): array
    {
        return $this->client->get($this->path('bank-accounts'));
    }

    /**
     * Add bank account
     */
    public function addBankAccount(array $data): array
    {
        return $this->client->post($this->path('bank-accounts'), $data);
    }

    /**
     * Set default bank account
     */
    public function setDefaultBankAccount(string $bankAccountId): array
    {
        return $this->client->post($this->path("bank-accounts/{$bankAccountId}/default"));
    }

    /**
     * Delete bank account
     */
    public function deleteBankAccount(string $bankAccountId): array
    {
        return $this->client->delete($this->path("bank-accounts/{$bankAccountId}"));
    }

    /**
     * Get payout settings
     */
    public function payoutSettings(): array
    {
        return $this->client->get($this->path('payout-settings'));
    }

    /**
     * Update payout settings
     */
    public function updatePayoutSettings(array $settings): array
    {
        return $this->client->put($this->path('payout-settings'), $settings);
    }

    /**
     * Get balance
     */
    public function balance(): array
    {
        return $this->client->get($this->path('balance'));
    }

    /**
     * Get balance transactions
     */
    public function balanceTransactions(array $params = []): array
    {
        return $this->client->get($this->path('balance/transactions'), $params);
    }

    /**
     * Create instant payout
     */
    public function instantPayout(int $amount, string $currency = 'eur'): array
    {
        return $this->client->post($this->path('payouts/instant'), [
            'amount' => $amount,
            'currency' => $currency,
        ]);
    }

    /**
     * Get transfer schedule
     */
    public function transferSchedule(): array
    {
        return $this->client->get($this->path('transfer-schedule'));
    }

    /**
     * Update transfer schedule
     */
    public function updateTransferSchedule(array $schedule): array
    {
        return $this->client->put($this->path('transfer-schedule'), $schedule);
    }

    /**
     * Get application fee rate
     */
    public function applicationFeeRate(): array
    {
        return $this->client->get($this->path('application-fee'));
    }

    /**
     * Get tax settings
     */
    public function taxSettings(): array
    {
        return $this->client->get($this->path('tax-settings'));
    }

    /**
     * Update tax settings
     */
    public function updateTaxSettings(array $settings): array
    {
        return $this->client->put($this->path('tax-settings'), $settings);
    }

    /**
     * Disconnect Stripe account
     */
    public function disconnect(): array
    {
        return $this->client->post($this->path('disconnect'));
    }

    /**
     * Check if fully onboarded
     */
    public function isFullyOnboarded(): bool
    {
        $status = $this->status();
        return ($status['data']['charges_enabled'] ?? false) && ($status['data']['payouts_enabled'] ?? false);
    }
}
