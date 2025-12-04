<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Key
    |--------------------------------------------------------------------------
    |
    | Your Sensei Partner API key. This is used for server-side authentication.
    | Keys starting with 'sk_' are secret keys and should never be exposed.
    | Keys starting with 'pk_' are public keys and can be used client-side.
    |
    | Supports both env variable names:
    |   - SENSEI_API_KEY (recommended, matches backend output)
    |   - SENSEI_PARTNER_API_KEY (legacy)
    |
    */
    'api_key' => env('SENSEI_API_KEY', env('SENSEI_PARTNER_API_KEY')),

    /*
    |--------------------------------------------------------------------------
    | Bearer Token
    |--------------------------------------------------------------------------
    |
    | Alternative authentication using a Bearer token. This is typically used
    | when authenticating on behalf of a specific partner user.
    |
    */
    'bearer_token' => env('SENSEI_BEARER_TOKEN', env('SENSEI_PARTNER_BEARER_TOKEN')),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL of the Sensei API. In production, this should be the main
    | API URL. For testing, you can use the staging URL.
    |
    | Supports both env variable names:
    |   - SENSEI_API_URL (recommended, matches backend output)
    |   - SENSEI_PARTNER_BASE_URL (legacy)
    |
    */
    'base_url' => env('SENSEI_API_URL', env('SENSEI_PARTNER_BASE_URL', 'https://api.senseitemple.com')),

    /*
    |--------------------------------------------------------------------------
    | Environment
    |--------------------------------------------------------------------------
    |
    | The Sensei environment: 'sandbox' for testing, 'production' for live.
    | This is used for logging and debugging purposes.
    |
    */
    'environment' => env('SENSEI_ENVIRONMENT', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The maximum time in seconds to wait for a response from the API.
    | Increase this if you're experiencing timeout errors on slow connections.
    |
    */
    'timeout' => env('SENSEI_PARTNER_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Connection Timeout
    |--------------------------------------------------------------------------
    |
    | The maximum time in seconds to wait when establishing a connection.
    |
    */
    'connect_timeout' => env('SENSEI_PARTNER_CONNECT_TIMEOUT', 10),

    /*
    |--------------------------------------------------------------------------
    | Max Retries
    |--------------------------------------------------------------------------
    |
    | The maximum number of times to retry a failed request before giving up.
    | This applies to transient errors like network issues.
    |
    */
    'max_retries' => env('SENSEI_PARTNER_MAX_RETRIES', 3),

    /*
    |--------------------------------------------------------------------------
    | SSL Verification
    |--------------------------------------------------------------------------
    |
    | Whether to verify SSL certificates. This should always be true in
    | production. Only set to false for local development with self-signed certs.
    |
    */
    'verify_ssl' => env('SENSEI_PARTNER_VERIFY_SSL', true),

    /*
    |--------------------------------------------------------------------------
    | Retry on Rate Limit
    |--------------------------------------------------------------------------
    |
    | Whether to automatically retry requests that receive a 429 rate limit
    | response. When enabled, the SDK will wait and retry with exponential backoff.
    |
    */
    'retry_on_rate_limit' => env('SENSEI_PARTNER_RETRY_ON_RATE_LIMIT', true),

    /*
    |--------------------------------------------------------------------------
    | HTTP Options
    |--------------------------------------------------------------------------
    |
    | Additional options to pass to the Guzzle HTTP client.
    | See: https://docs.guzzlephp.org/en/stable/request-options.html
    |
    */
    'http_options' => [
        // 'proxy' => env('SENSEI_PARTNER_PROXY'),
    ],
];
