<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK;

use InvalidArgumentException;

/**
 * SDK Configuration (Immutable)
 *
 * Handles API key validation, base URL, timeouts, and HTTP options.
 * All configuration is set in constructor and cannot be modified.
 */
final class Configuration
{
    public const SDK_VERSION = '1.0.0';

    private ?string $apiKey;
    private ?string $bearerToken;
    private string $baseUrl;
    private int $timeout;
    private int $connectTimeout;
    private int $maxRetries;
    private bool $verifySSL;
    private bool $retryOnRateLimit;
    private array $httpOptions;

    public function __construct(
        ?string $apiKey = null,
        ?string $bearerToken = null,
        string $baseUrl = 'https://api.senseitemple.com/api',
        int $timeout = 30,
        int $connectTimeout = 10,
        int $maxRetries = 3,
        bool $verifySSL = true,
        bool $retryOnRateLimit = true,
        array $httpOptions = []
    ) {
        if (empty($apiKey) && empty($bearerToken)) {
            throw new InvalidArgumentException('Either API key or Bearer token must be provided');
        }

        if (empty($baseUrl)) {
            throw new InvalidArgumentException('Base URL cannot be empty');
        }

        $this->apiKey = $apiKey;
        $this->bearerToken = $bearerToken;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->timeout = $timeout;
        $this->connectTimeout = $connectTimeout;
        $this->maxRetries = max(0, $maxRetries);
        $this->verifySSL = $verifySSL;
        $this->retryOnRateLimit = $retryOnRateLimit;
        $this->httpOptions = $httpOptions;
    }

    /**
     * Create configuration from array
     */
    public static function fromArray(array $config): self
    {
        return new self(
            apiKey: $config['api_key'] ?? null,
            bearerToken: $config['bearer_token'] ?? null,
            baseUrl: $config['base_url'] ?? 'https://api.senseitemple.com/api',
            timeout: $config['timeout'] ?? 30,
            connectTimeout: $config['connect_timeout'] ?? 10,
            maxRetries: $config['max_retries'] ?? 3,
            verifySSL: $config['verify_ssl'] ?? true,
            retryOnRateLimit: $config['retry_on_rate_limit'] ?? true,
            httpOptions: $config['http_options'] ?? []
        );
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function getBearerToken(): ?string
    {
        return $this->bearerToken;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function getConnectTimeout(): int
    {
        return $this->connectTimeout;
    }

    public function getMaxRetries(): int
    {
        return $this->maxRetries;
    }

    public function shouldVerifySSL(): bool
    {
        return $this->verifySSL;
    }

    public function shouldRetryOnRateLimit(): bool
    {
        return $this->retryOnRateLimit;
    }

    public function getHttpOptions(): array
    {
        return $this->httpOptions;
    }

    public function getSdkVersion(): string
    {
        return self::SDK_VERSION;
    }

    /**
     * Check if using a secret key (server-side only)
     */
    public function isSecretKey(): bool
    {
        return $this->apiKey && str_starts_with($this->apiKey, 'sk_');
    }

    /**
     * Check if using a public key (client-safe)
     */
    public function isPublicKey(): bool
    {
        return $this->apiKey && str_starts_with($this->apiKey, 'pk_');
    }

    /**
     * Check if using live mode keys
     */
    public function isLiveMode(): bool
    {
        return $this->apiKey && str_contains($this->apiKey, '_live_');
    }

    /**
     * Check if using test mode keys
     */
    public function isTestMode(): bool
    {
        return $this->apiKey && str_contains($this->apiKey, '_test_');
    }

    /**
     * Create new configuration with different API key
     */
    public function withApiKey(string $apiKey): self
    {
        return new self(
            apiKey: $apiKey,
            bearerToken: $this->bearerToken,
            baseUrl: $this->baseUrl,
            timeout: $this->timeout,
            connectTimeout: $this->connectTimeout,
            maxRetries: $this->maxRetries,
            verifySSL: $this->verifySSL,
            retryOnRateLimit: $this->retryOnRateLimit,
            httpOptions: $this->httpOptions
        );
    }

    /**
     * Create new configuration with different bearer token
     */
    public function withBearerToken(string $bearerToken): self
    {
        return new self(
            apiKey: $this->apiKey,
            bearerToken: $bearerToken,
            baseUrl: $this->baseUrl,
            timeout: $this->timeout,
            connectTimeout: $this->connectTimeout,
            maxRetries: $this->maxRetries,
            verifySSL: $this->verifySSL,
            retryOnRateLimit: $this->retryOnRateLimit,
            httpOptions: $this->httpOptions
        );
    }

    /**
     * Create new configuration with different base URL
     */
    public function withBaseUrl(string $baseUrl): self
    {
        return new self(
            apiKey: $this->apiKey,
            bearerToken: $this->bearerToken,
            baseUrl: $baseUrl,
            timeout: $this->timeout,
            connectTimeout: $this->connectTimeout,
            maxRetries: $this->maxRetries,
            verifySSL: $this->verifySSL,
            retryOnRateLimit: $this->retryOnRateLimit,
            httpOptions: $this->httpOptions
        );
    }
}
