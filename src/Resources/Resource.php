<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK\Resources;

use Sensei\PartnerSDK\PartnerClient;
use Sensei\PartnerSDK\Support\PaginatedResponse;

/**
 * Base resource class
 */
abstract class Resource
{
    protected PartnerClient $client;
    protected string $basePath = '';

    public function __construct(PartnerClient $client)
    {
        $this->client = $client;
    }

    /**
     * Build full endpoint path
     */
    protected function path(string $endpoint = ''): string
    {
        return rtrim($this->basePath . '/' . ltrim($endpoint, '/'), '/');
    }

    /**
     * Get paginated results
     */
    protected function paginate(string $endpoint, array $params = []): PaginatedResponse
    {
        $response = $this->client->get($endpoint, $params);
        return new PaginatedResponse($response, $this->client, $endpoint, $params);
    }

    /**
     * List resources with optional filters
     */
    protected function list(array $params = []): array
    {
        return $this->client->get($this->path(), $params);
    }

    /**
     * Get single resource by ID
     */
    protected function find(int|string $id): array
    {
        return $this->client->get($this->path((string) $id));
    }

    /**
     * Create new resource
     */
    protected function store(array $data): array
    {
        return $this->client->post($this->path(), $data);
    }

    /**
     * Update existing resource
     */
    protected function update(int|string $id, array $data): array
    {
        return $this->client->put($this->path((string) $id), $data);
    }

    /**
     * Delete resource
     */
    protected function destroy(int|string $id): array
    {
        return $this->client->delete($this->path((string) $id));
    }
}
