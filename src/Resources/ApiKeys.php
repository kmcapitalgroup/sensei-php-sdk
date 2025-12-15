<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK\Resources;

use Sensei\PartnerSDK\Support\PaginatedResponse;

/**
 * API Key management resource
 *
 * Create and manage API keys for partner integrations
 */
class ApiKeys extends Resource
{
    protected string $basePath = 'v1/partners/api-keys';

    /**
     * List all API keys
     */
    public function all(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path(), $params);
    }

    /**
     * Get a specific API key
     */
    public function get(int $id): array
    {
        return $this->find($id);
    }

    /**
     * Create a new API key
     *
     * @param array $data [
     *   'name' => string (required) - Human-readable name,
     *   'permissions' => array - List of permissions,
     *   'expires_at' => string|null - Expiration date (ISO 8601),
     *   'rate_limit' => int|null - Custom rate limit per minute,
     *   'allowed_ips' => array|null - Whitelisted IP addresses,
     *   'allowed_domains' => array|null - Whitelisted domains for CORS,
     * ]
     */
    public function create(array $data): array
    {
        return $this->store($data);
    }

    /**
     * Update an API key
     */
    public function updateKey(int $id, array $data): array
    {
        return $this->update($id, $data);
    }

    /**
     * Delete/revoke an API key
     */
    public function delete(int $id): array
    {
        return $this->destroy($id);
    }

    /**
     * Regenerate an API key (creates new secret)
     */
    public function regenerate(int $id): array
    {
        return $this->client->post($this->path("{$id}/regenerate"));
    }

    /**
     * Verify an API key is valid
     */
    public function verify(string $apiKey): array
    {
        return $this->client->post($this->path('verify'), ['api_key' => $apiKey]);
    }

    /**
     * Get API key usage statistics
     */
    public function usage(int $id, array $params = []): array
    {
        return $this->client->get($this->path("{$id}/usage"), $params);
    }

    /**
     * Get available permissions
     */
    public function permissions(): array
    {
        return $this->client->get($this->path('permissions'));
    }

    /**
     * Update API key permissions
     */
    public function updatePermissions(int $id, array $permissions): array
    {
        return $this->client->put($this->path("{$id}/permissions"), ['permissions' => $permissions]);
    }

    /**
     * Enable an API key
     */
    public function enable(int $id): array
    {
        return $this->client->post($this->path("{$id}/enable"));
    }

    /**
     * Disable an API key
     */
    public function disable(int $id): array
    {
        return $this->client->post($this->path("{$id}/disable"));
    }

    /**
     * Get request logs for an API key
     */
    public function logs(int $id, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("{$id}/logs"), $params);
    }
}
