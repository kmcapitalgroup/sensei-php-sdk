<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK\Resources;

/**
 * SSO (Single Sign-On) management resource
 *
 * Configure OAuth 2.0 / OpenID Connect SSO for seamless authentication
 * between your platform and Sensei Temple.
 *
 * SECURITY: This resource implements OAuth 2.0 with PKCE (RFC 7636)
 * which is mandatory for all authorization flows.
 */
class Sso extends Resource
{
    protected string $basePath = 'v1/partners/sso';

    // =========================================================================
    // SSO Settings Management
    // =========================================================================

    /**
     * Get current SSO settings for your tenant
     *
     * @return array {settings: array, stats: array}
     */
    public function getSettings(): array
    {
        return $this->client->get($this->path('settings'));
    }

    /**
     * Get SSO statistics
     *
     * @return array {stats: {total_connections, active_users, last_30_days}}
     */
    public function getStats(): array
    {
        return $this->client->get($this->path('stats'));
    }

    /**
     * Enable SSO
     *
     * If no credentials exist, they will be automatically generated.
     * IMPORTANT: Save the client_secret immediately - it's only shown once!
     *
     * @return array Updated settings (may include client_secret if newly generated)
     */
    public function enable(): array
    {
        return $this->client->post($this->path('toggle'), ['enabled' => true]);
    }

    /**
     * Disable SSO
     *
     * @return array Updated settings
     */
    public function disable(): array
    {
        return $this->client->post($this->path('toggle'), ['enabled' => false]);
    }

    /**
     * Toggle SSO on/off
     *
     * @param bool $enabled Whether to enable or disable SSO
     * @return array Updated settings
     */
    public function toggle(bool $enabled): array
    {
        return $this->client->post($this->path('toggle'), ['enabled' => $enabled]);
    }

    /**
     * Regenerate client secret only (keeps existing client_id)
     *
     * Use this when you need to rotate the secret without changing client_id.
     *
     * IMPORTANT: The client_secret is only returned ONCE in this response.
     * Store it securely - it cannot be retrieved again.
     *
     * @return array {settings: {client_id, client_secret, ...}, message: string}
     */
    public function regenerateSecret(): array
    {
        return $this->client->post($this->path('regenerate-secret'));
    }

    /**
     * Update allowed redirect URIs
     *
     * @param array $uris List of allowed redirect URIs (must be HTTPS in production)
     * @return array Updated settings
     */
    public function setRedirectUris(array $uris): array
    {
        return $this->client->put($this->path('redirect-urls'), [
            'redirect_urls' => $uris,
        ]);
    }

    /**
     * Add a redirect URI to the allowed list
     *
     * @param string $uri Redirect URI to add
     * @return array Updated settings
     */
    public function addRedirectUri(string $uri): array
    {
        $response = $this->getSettings();
        $currentUris = $response['settings']['redirect_urls'] ?? [];

        if (!in_array($uri, $currentUris, true)) {
            $currentUris[] = $uri;
        }

        return $this->setRedirectUris($currentUris);
    }

    /**
     * Remove a redirect URI from the allowed list
     *
     * @param string $uri Redirect URI to remove
     * @return array Updated settings
     */
    public function removeRedirectUri(string $uri): array
    {
        $response = $this->getSettings();
        $currentUris = $response['settings']['redirect_urls'] ?? [];

        $currentUris = array_filter($currentUris, fn($u) => $u !== $uri);

        return $this->setRedirectUris(array_values($currentUris));
    }

    // =========================================================================
    // OAuth 2.0 Flow Helpers (use /oauth/* endpoints)
    // =========================================================================

    /**
     * Build authorization URL for OAuth 2.0 flow with PKCE
     *
     * This is a client-side helper - the actual authorization happens
     * in the user's browser.
     *
     * @param string $clientId Your OAuth client ID
     * @param string $redirectUri Your registered redirect URI
     * @param string $codeChallenge PKCE code challenge (S256 hashed)
     * @param array $scopes Requested scopes (default: openid, profile, email)
     * @param string|null $state CSRF protection state parameter
     * @return string Full authorization URL
     */
    public function buildAuthorizationUrl(
        string $clientId,
        string $redirectUri,
        string $codeChallenge,
        array $scopes = ['openid', 'profile', 'email'],
        ?string $state = null
    ): string {
        $baseUrl = rtrim($this->client->getConfig()->getBaseUrl(), '/');

        $params = [
            'response_type' => 'code',
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'scope' => implode(' ', $scopes),
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
        ];

        if ($state !== null) {
            $params['state'] = $state;
        }

        return $baseUrl . '/oauth/authorize?' . http_build_query($params);
    }

    /**
     * Generate PKCE code verifier and challenge
     *
     * Use this helper to create the PKCE parameters needed for authorization.
     *
     * @return array {code_verifier: string, code_challenge: string}
     */
    public static function generatePkce(): array
    {
        // Generate cryptographically secure code verifier (43-128 chars)
        $codeVerifier = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');

        // Generate code challenge using S256 method
        $codeChallenge = rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');

        return [
            'code_verifier' => $codeVerifier,
            'code_challenge' => $codeChallenge,
        ];
    }

    /**
     * Exchange authorization code for tokens
     *
     * @param string $code Authorization code from callback
     * @param string $clientId Your OAuth client ID
     * @param string $clientSecret Your OAuth client secret
     * @param string $redirectUri The same redirect URI used in authorization
     * @param string $codeVerifier The original PKCE code verifier
     * @return array {access_token, token_type, expires_in, scope, refresh_token?}
     */
    public function exchangeCode(
        string $code,
        string $clientId,
        string $clientSecret,
        string $redirectUri,
        string $codeVerifier
    ): array {
        return $this->client->post('oauth/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'code_verifier' => $codeVerifier,
        ]);
    }

    /**
     * Refresh an access token
     *
     * @param string $refreshToken The refresh token
     * @param string $clientId Your OAuth client ID
     * @param string $clientSecret Your OAuth client secret
     * @return array {access_token, token_type, expires_in, scope, refresh_token?}
     */
    public function refreshToken(
        string $refreshToken,
        string $clientId,
        string $clientSecret
    ): array {
        return $this->client->post('oauth/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ]);
    }

    /**
     * Get user info using access token
     *
     * NOTE: This requires the access token from the SSO flow,
     * not the partner API token.
     *
     * @param string $accessToken OAuth access token
     * @return array User info based on granted scopes
     */
    public function getUserInfo(string $accessToken): array
    {
        return $this->client->request('GET', 'oauth/userinfo', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ]);
    }

    /**
     * Revoke a token
     *
     * @param string $token The token to revoke
     * @param string $tokenTypeHint Either 'access_token' or 'refresh_token'
     * @return array
     */
    public function revokeToken(string $token, string $tokenTypeHint = 'access_token'): array
    {
        return $this->client->post('oauth/revoke', [
            'token' => $token,
            'token_type_hint' => $tokenTypeHint,
        ]);
    }

    /**
     * Get OpenID Connect discovery document URL
     *
     * @return string URL to .well-known/openid-configuration
     */
    public function getDiscoveryUrl(): string
    {
        $baseUrl = rtrim($this->client->getConfig()->getBaseUrl(), '/');
        return $baseUrl . '/.well-known/openid-configuration';
    }
}
