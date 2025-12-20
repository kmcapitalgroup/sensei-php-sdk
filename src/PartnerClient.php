<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException as GuzzleServerException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Sensei\PartnerSDK\Exceptions\AuthenticationException;
use Sensei\PartnerSDK\Exceptions\NotFoundException;
use Sensei\PartnerSDK\Exceptions\RateLimitException;
use Sensei\PartnerSDK\Exceptions\SenseiPartnerException;
use Sensei\PartnerSDK\Exceptions\ServerException;
use Sensei\PartnerSDK\Exceptions\ValidationException;
use Sensei\PartnerSDK\Resources\Affiliates;
use Sensei\PartnerSDK\Resources\Alliances;
use Sensei\PartnerSDK\Resources\Analytics;
use Sensei\PartnerSDK\Resources\ApiKeys;
use Sensei\PartnerSDK\Resources\Certificates;
use Sensei\PartnerSDK\Resources\Compliance;
use Sensei\PartnerSDK\Resources\Coupons;
use Sensei\PartnerSDK\Resources\Dashboard;
use Sensei\PartnerSDK\Resources\Events;
use Sensei\PartnerSDK\Resources\Forums;
use Sensei\PartnerSDK\Resources\Gamification;
use Sensei\PartnerSDK\Resources\Guilds;
use Sensei\PartnerSDK\Resources\Media;
use Sensei\PartnerSDK\Resources\Messages;
use Sensei\PartnerSDK\Resources\Notifications;
use Sensei\PartnerSDK\Resources\Payments;
use Sensei\PartnerSDK\Resources\Products;
use Sensei\PartnerSDK\Resources\Profile;
use Sensei\PartnerSDK\Resources\Reviews;
use Sensei\PartnerSDK\Resources\Settings;
use Sensei\PartnerSDK\Resources\Sso;
use Sensei\PartnerSDK\Resources\StripeConnect;
use Sensei\PartnerSDK\Resources\Subscriptions;
use Sensei\PartnerSDK\Resources\TrustScore;
use Sensei\PartnerSDK\Resources\Users;
use Sensei\PartnerSDK\Resources\UserStripeConnect;
use Sensei\PartnerSDK\Resources\Webhooks;

/**
 * Main Partner SDK Client
 *
 * @property-read Affiliates $affiliates Affiliate program management
 * @property-read Alliances $alliances Alliance/federation management
 * @property-read Analytics $analytics Analytics and reporting
 * @property-read ApiKeys $apiKeys API Key management
 * @property-read Certificates $certificates Certificate management
 * @property-read Compliance $compliance GDPR, DPA and tax compliance
 * @property-read Coupons $coupons Discount coupons and promotions
 * @property-read Dashboard $dashboard Dashboard statistics
 * @property-read Events $events Live events and webinars
 * @property-read Forums $forums Discussion forums
 * @property-read Gamification $gamification XP, badges, achievements
 * @property-read Guilds $guilds Guild/community management
 * @property-read Media $media Media library management
 * @property-read Messages $messages Messaging system
 * @property-read Notifications $notifications Push, email, SMS notifications
 * @property-read Payments $payments Payment management
 * @property-read Products $products Product management
 * @property-read Profile $profile Partner profile management
 * @property-read Reviews $reviews Product reviews and ratings
 * @property-read Settings $settings Partner settings
 * @property-read Sso $sso SSO/OAuth configuration
 * @property-read StripeConnect $stripeConnect Stripe Connect integration (Partner level)
 * @property-read Subscriptions $subscriptions Subscription management
 * @property-read TrustScore $trustScore Trust score and reputation system
 * @property-read Users $users User/customer management
 * @property-read UserStripeConnect $userStripeConnect Stripe Connect for users (sellers)
 * @property-read Webhooks $webhooks Webhook management
 */
class PartnerClient
{
    private Configuration $config;
    private Client $httpClient;
    private array $resources = [];
    private int $retryCount = 0;

    /**
     * Create a new Partner SDK client
     */
    public function __construct(Configuration $config)
    {
        $this->config = $config;
        $this->httpClient = $this->createHttpClient();
    }

    /**
     * Create Partner SDK from array configuration
     */
    public static function create(array $config): self
    {
        return new self(Configuration::fromArray($config));
    }

    /**
     * Create HTTP client with configured options
     */
    private function createHttpClient(): Client
    {
        // Base client options
        $options = [
            'base_uri' => rtrim($this->config->getBaseUrl(), '/') . '/',
            'timeout' => $this->config->getTimeout(),
            'connect_timeout' => $this->config->getConnectTimeout(),
            'verify' => $this->config->shouldVerifySSL(),
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'User-Agent' => 'Sensei-Partner-SDK/' . $this->config->getSdkVersion(),
            ],
            'http_errors' => false, // We handle errors ourselves
        ];

        // Merge custom HTTP options (allows proxy, handler, etc.)
        $customOptions = $this->config->getHttpOptions();
        if (!empty($customOptions)) {
            $options = array_merge($options, $customOptions);
        }

        return new Client($options);
    }

    /**
     * Get configuration
     */
    public function getConfig(): Configuration
    {
        return $this->config;
    }

    /**
     * Make HTTP request with authentication
     *
     * @throws SenseiPartnerException
     */
    public function request(string $method, string $endpoint, array $options = []): array
    {
        $options = $this->addAuthentication($options);

        try {
            $response = $this->httpClient->request($method, ltrim($endpoint, '/'), $options);
            return $this->handleResponse($response, $method, $endpoint, $options);
        } catch (ConnectException $e) {
            throw new SenseiPartnerException(
                'Connection failed: ' . $e->getMessage(),
                0,
                $e
            );
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                return $this->handleResponse($e->getResponse(), $method, $endpoint, $options);
            }
            throw new SenseiPartnerException(
                'Request failed: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Add authentication headers to request
     */
    private function addAuthentication(array $options): array
    {
        $headers = $options['headers'] ?? [];

        if ($this->config->getApiKey()) {
            $headers['X-API-Key'] = $this->config->getApiKey();
        }

        if ($this->config->getBearerToken()) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getBearerToken();
        }

        $options['headers'] = $headers;
        return $options;
    }

    /**
     * Handle HTTP response
     *
     * @throws SenseiPartnerException
     */
    private function handleResponse(
        ResponseInterface $response,
        string $method,
        string $endpoint,
        array $options
    ): array {
        $statusCode = $response->getStatusCode();
        $body = (string) $response->getBody();
        $data = json_decode($body, true) ?? [];

        // Success responses
        if ($statusCode >= 200 && $statusCode < 300) {
            $this->retryCount = 0;
            return $data;
        }

        // Handle error responses
        $message = $data['message'] ?? $data['error'] ?? 'Unknown error';

        switch ($statusCode) {
            case 401:
            case 403:
                throw new AuthenticationException($message, $statusCode, null, $response, $data);

            case 404:
                throw new NotFoundException($message, $statusCode, null, $response, $data);

            case 422:
                throw new ValidationException($message, $statusCode, null, $response, $data);

            case 429:
                // Retry-After header is in seconds per HTTP specification
                $retryAfterSeconds = (int) ($response->getHeader('Retry-After')[0] ?? 60);

                // Auto-retry with exponential backoff if enabled (uses configured max_retries)
                if ($this->config->shouldRetryOnRateLimit() && $this->retryCount < $this->config->getMaxRetries()) {
                    $this->retryCount++;
                    // Exponential backoff: 2^retry seconds (2s, 4s, 8s...)
                    $backoffSeconds = pow(2, $this->retryCount);
                    // Use the smaller of Retry-After or backoff, both in seconds
                    $waitTimeSeconds = min($retryAfterSeconds, $backoffSeconds);
                    // usleep() expects microseconds (1 second = 1,000,000 microseconds)
                    usleep((int) ($waitTimeSeconds * 1_000_000));
                    return $this->request($method, $endpoint, $options);
                }

                throw new RateLimitException($message, $retryAfterSeconds);

            case 500:
            case 502:
            case 503:
            case 504:
                throw new ServerException($message, $statusCode, null, $response, $data);

            default:
                throw new SenseiPartnerException($message, $statusCode, null, $response, $data);
        }
    }

    /**
     * Convenience methods for HTTP verbs
     */
    public function get(string $endpoint, array $query = []): array
    {
        $options = $query ? ['query' => $query] : [];
        return $this->request('GET', $endpoint, $options);
    }

    public function post(string $endpoint, array $data = []): array
    {
        return $this->request('POST', $endpoint, ['json' => $data]);
    }

    public function put(string $endpoint, array $data = []): array
    {
        return $this->request('PUT', $endpoint, ['json' => $data]);
    }

    public function patch(string $endpoint, array $data = []): array
    {
        return $this->request('PATCH', $endpoint, ['json' => $data]);
    }

    public function delete(string $endpoint, array $data = []): array
    {
        $options = $data ? ['json' => $data] : [];
        return $this->request('DELETE', $endpoint, $options);
    }

    /**
     * Upload file
     */
    public function upload(string $endpoint, string $filePath, string $fieldName = 'file', array $data = []): array
    {
        $multipart = [
            [
                'name' => $fieldName,
                'contents' => fopen($filePath, 'r'),
                'filename' => basename($filePath),
            ],
        ];

        foreach ($data as $key => $value) {
            $multipart[] = [
                'name' => $key,
                'contents' => is_array($value) ? json_encode($value) : (string) $value,
            ];
        }

        return $this->request('POST', $endpoint, [
            'multipart' => $multipart,
            'headers' => ['Content-Type' => null], // Let Guzzle set multipart boundary
        ]);
    }

    /**
     * Magic getter for resources
     */
    public function __get(string $name)
    {
        if (!isset($this->resources[$name])) {
            $this->resources[$name] = $this->createResource($name);
        }
        return $this->resources[$name];
    }

    /**
     * Create resource instance
     */
    private function createResource(string $name)
    {
        $resourceMap = [
            'affiliates' => Affiliates::class,
            'alliances' => Alliances::class,
            'analytics' => Analytics::class,
            'apiKeys' => ApiKeys::class,
            'certificates' => Certificates::class,
            'compliance' => Compliance::class,
            'coupons' => Coupons::class,
            'dashboard' => Dashboard::class,
            'events' => Events::class,
            'forums' => Forums::class,
            'gamification' => Gamification::class,
            'guilds' => Guilds::class,
            'media' => Media::class,
            'messages' => Messages::class,
            'notifications' => Notifications::class,
            'payments' => Payments::class,
            'products' => Products::class,
            'profile' => Profile::class,
            'reviews' => Reviews::class,
            'settings' => Settings::class,
            'sso' => Sso::class,
            'stripeConnect' => StripeConnect::class,
            'subscriptions' => Subscriptions::class,
            'trustScore' => TrustScore::class,
            'users' => Users::class,
            'userStripeConnect' => UserStripeConnect::class,
            'webhooks' => Webhooks::class,
        ];

        if (!isset($resourceMap[$name])) {
            throw new \InvalidArgumentException("Unknown resource: {$name}");
        }

        return new $resourceMap[$name]($this);
    }

    /**
     * Check if client is authenticated
     */
    public function isAuthenticated(): bool
    {
        return $this->config->getApiKey() !== null || $this->config->getBearerToken() !== null;
    }

    /**
     * Create new client with different authentication
     */
    public function withApiKey(string $apiKey): self
    {
        return new self($this->config->withApiKey($apiKey));
    }

    public function withBearerToken(string $token): self
    {
        return new self($this->config->withBearerToken($token));
    }
}
