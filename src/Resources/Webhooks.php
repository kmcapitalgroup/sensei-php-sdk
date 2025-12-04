<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK\Resources;

use Sensei\PartnerSDK\Support\PaginatedResponse;

/**
 * Webhook management resource
 *
 * Create and manage webhook endpoints
 */
class Webhooks extends Resource
{
    protected string $basePath = 'partner/webhooks';

    /**
     * List all webhooks
     */
    public function all(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path(), $params);
    }

    /**
     * Get a specific webhook
     */
    public function get(int $id): array
    {
        return $this->find($id);
    }

    /**
     * Create a webhook endpoint
     *
     * @param array $data [
     *   'url' => string (required) - Endpoint URL,
     *   'events' => array (required) - Events to subscribe to,
     *   'description' => string|null - Human-readable description,
     *   'enabled' => bool - Whether webhook is active,
     *   'secret' => string|null - Custom signing secret (auto-generated if not provided),
     * ]
     */
    public function create(array $data): array
    {
        return $this->store($data);
    }

    /**
     * Update a webhook endpoint
     */
    public function updateWebhook(int $id, array $data): array
    {
        return $this->update($id, $data);
    }

    /**
     * Delete a webhook endpoint
     */
    public function delete(int $id): array
    {
        return $this->destroy($id);
    }

    /**
     * Enable a webhook
     */
    public function enable(int $id): array
    {
        return $this->client->post($this->path("{$id}/enable"));
    }

    /**
     * Disable a webhook
     */
    public function disable(int $id): array
    {
        return $this->client->post($this->path("{$id}/disable"));
    }

    /**
     * Regenerate webhook secret
     */
    public function regenerateSecret(int $id): array
    {
        return $this->client->post($this->path("{$id}/regenerate-secret"));
    }

    /**
     * Test webhook endpoint
     */
    public function test(int $id, string $eventType = 'test.event'): array
    {
        return $this->client->post($this->path("{$id}/test"), ['event_type' => $eventType]);
    }

    /**
     * Get available event types
     */
    public function eventTypes(): array
    {
        return $this->client->get($this->path('event-types'));
    }

    /**
     * Get webhook delivery attempts
     */
    public function deliveries(int $webhookId, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("{$webhookId}/deliveries"), $params);
    }

    /**
     * Get a specific delivery attempt
     */
    public function delivery(int $webhookId, int $deliveryId): array
    {
        return $this->client->get($this->path("{$webhookId}/deliveries/{$deliveryId}"));
    }

    /**
     * Retry a failed delivery
     */
    public function retryDelivery(int $webhookId, int $deliveryId): array
    {
        return $this->client->post($this->path("{$webhookId}/deliveries/{$deliveryId}/retry"));
    }

    /**
     * Get webhook logs
     */
    public function logs(int $webhookId, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("{$webhookId}/logs"), $params);
    }

    /**
     * Get webhook statistics
     */
    public function statistics(int $webhookId): array
    {
        return $this->client->get($this->path("{$webhookId}/statistics"));
    }

    /**
     * Verify webhook signature
     */
    public static function verifySignature(string $payload, string $signature, string $secret): bool
    {
        $expectedSignature = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Parse webhook payload
     */
    public static function parsePayload(string $payload): array
    {
        $data = json_decode($payload, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON payload: ' . json_last_error_msg());
        }
        return $data;
    }

    /**
     * Subscribe to specific events
     */
    public function subscribe(int $id, array $events): array
    {
        return $this->client->post($this->path("{$id}/subscribe"), ['events' => $events]);
    }

    /**
     * Unsubscribe from specific events
     */
    public function unsubscribe(int $id, array $events): array
    {
        return $this->client->post($this->path("{$id}/unsubscribe"), ['events' => $events]);
    }

    /**
     * Get subscribed events for a webhook
     */
    public function subscribedEvents(int $id): array
    {
        return $this->client->get($this->path("{$id}/events"));
    }
}
