<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK\Laravel;

use Illuminate\Support\ServiceProvider;
use Sensei\PartnerSDK\Configuration;
use Sensei\PartnerSDK\PartnerClient;

class SenseiPartnerServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/sensei-partner.php',
            'sensei-partner'
        );

        // Register Configuration as singleton
        $this->app->singleton(Configuration::class, function ($app) {
            $config = $app['config']['sensei-partner'];

            return new Configuration(
                apiKey: $config['api_key'] ?? null,
                bearerToken: $config['bearer_token'] ?? null,
                baseUrl: $config['base_url'] ?? 'https://api.senseitemple.com',
                timeout: $config['timeout'] ?? 30,
                connectTimeout: $config['connect_timeout'] ?? 10,
                maxRetries: $config['max_retries'] ?? 3,
                verifySSL: $config['verify_ssl'] ?? true,
                retryOnRateLimit: $config['retry_on_rate_limit'] ?? true,
                httpOptions: $config['http_options'] ?? []
            );
        });

        // Register PartnerClient as singleton
        $this->app->singleton(PartnerClient::class, function ($app) {
            return new PartnerClient($app->make(Configuration::class));
        });

        // Alias for easier access
        $this->app->alias(PartnerClient::class, 'sensei-partner');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/sensei-partner.php' => config_path('sensei-partner.php'),
            ], 'sensei-partner-config');
        }
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            Configuration::class,
            PartnerClient::class,
            'sensei-partner',
        ];
    }
}
