<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK\Resources;

/**
 * User Stripe Connect integration resource
 *
 * Allow tenant users to become sellers and receive payments
 * for services, events, formations, mentoring, etc.
 *
 * IMPORTANT: This requires a user token, not the partner API key.
 * Use the token returned from signupAndLink() or loginAndLink().
 */
class UserStripeConnect extends Resource
{
    protected string $basePath = 'v1/{tenant}/user/stripe-connect';

    /**
     * Start Stripe Connect onboarding for a user
     *
     * Creates a Stripe Connect Express account for the user
     * and returns an onboarding URL to complete verification.
     *
     * @param array $data Onboarding data:
     *   - country (optional): 2-letter country code (default: tenant default)
     *   - business_type (optional): 'individual' or 'company'
     *   - company_name (required if business_type is 'company')
     *
     * @return array Response containing:
     *   - message: Success message
     *   - account_id: Stripe Connect account ID
     *   - onboarding_url: URL to redirect user for Stripe onboarding
     *
     * @example
     * // Using a user's auth token (not partner API key)
     * $userClient = new SenseiClient([
     *     'api_key' => $userToken, // Token from signupAndLink()
     *     'base_url' => 'https://api.sensei.com/api',
     *     'tenant' => 'your-tenant',
     * ]);
     *
     * $response = $userClient->userStripeConnect->onboard([
     *     'country' => 'FR',
     *     'business_type' => 'individual',
     * ]);
     *
     * // Redirect user to complete onboarding
     * header('Location: ' . $response['data']['onboarding_url']);
     */
    public function onboard(array $data = []): array
    {
        return $this->client->post($this->path('onboard'), $data);
    }

    /**
     * Get Stripe Connect account status
     *
     * Check if the user has a connected Stripe account and its status.
     *
     * @return array Response containing:
     *   - connected: Whether an account is linked
     *   - onboarded: Whether onboarding is complete
     *   - account_id: Stripe account ID
     *   - status: Account capabilities status
     *   - requirements: Pending verification requirements
     *
     * @example
     * $status = $userClient->userStripeConnect->status();
     *
     * if ($status['data']['onboarded']) {
     *     // User can receive payments
     * } else if ($status['data']['connected']) {
     *     // User started but didn't complete onboarding
     * }
     */
    public function status(): array
    {
        return $this->client->get($this->path('status'));
    }

    /**
     * Get Stripe Express Dashboard link
     *
     * Returns a link to the Stripe Express dashboard where
     * the user can view their earnings, payouts, and settings.
     *
     * @return array Response containing:
     *   - dashboard_url: URL to Stripe Express dashboard
     *
     * @throws \Exception If user hasn't completed onboarding
     *
     * @example
     * $response = $userClient->userStripeConnect->dashboard();
     * // Redirect or open in new tab
     * window.open($response['data']['dashboard_url']);
     */
    public function dashboard(): array
    {
        return $this->client->get($this->path('dashboard'));
    }

    /**
     * Get account balance
     *
     * Returns the user's Stripe Connect account balance
     * (available and pending funds).
     *
     * @return array Response containing:
     *   - available: Array of available balances per currency
     *   - pending: Array of pending balances per currency
     *
     * @example
     * $balance = $userClient->userStripeConnect->balance();
     * echo "Available: " . $balance['data']['available'][0]['amount'] . " EUR";
     * echo "Pending: " . $balance['data']['pending'][0]['amount'] . " EUR";
     */
    public function balance(): array
    {
        return $this->client->get($this->path('balance'));
    }

    /**
     * Refresh onboarding link
     *
     * If the onboarding link expired, generate a new one.
     *
     * @return array Response containing:
     *   - onboarding_url: New URL to continue onboarding
     *
     * @example
     * $response = $userClient->userStripeConnect->refresh();
     * header('Location: ' . $response['data']['onboarding_url']);
     */
    public function refresh(): array
    {
        return $this->client->post($this->path('refresh'));
    }

    /**
     * Disconnect Stripe Connect account
     *
     * Unlinks the Stripe Connect account from the user.
     * Note: The Stripe account itself is not deleted.
     *
     * @return array Response containing success message
     *
     * @throws \Exception If user has active paid services
     *
     * @example
     * $userClient->userStripeConnect->disconnect();
     */
    public function disconnect(): array
    {
        return $this->client->delete($this->path(''));
    }

    /**
     * Check if user can receive payments
     *
     * Helper method to quickly check if user is fully onboarded.
     *
     * @return bool True if user can receive payments
     *
     * @example
     * if ($userClient->userStripeConnect->canReceivePayments()) {
     *     // Show "Create paid service" button
     * } else {
     *     // Show "Connect Stripe" button
     * }
     */
    public function canReceivePayments(): bool
    {
        try {
            $status = $this->status();
            return ($status['data']['onboarded'] ?? false) === true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
