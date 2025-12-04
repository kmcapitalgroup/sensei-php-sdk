<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK\Support;

use Sensei\PartnerSDK\PartnerClient;
use ArrayAccess;
use Countable;
use Iterator;

/**
 * Paginated API response handler
 */
class PaginatedResponse implements ArrayAccess, Countable, Iterator
{
    private array $data;
    private array $meta;
    private array $links;
    private PartnerClient $client;
    private string $endpoint;
    private array $params;
    private int $position = 0;

    public function __construct(array $response, PartnerClient $client, string $endpoint, array $params = [])
    {
        $this->data = $response['data'] ?? $response;
        $this->meta = $response['meta'] ?? [];
        $this->links = $response['links'] ?? [];
        $this->client = $client;
        $this->endpoint = $endpoint;
        $this->params = $params;
    }

    /**
     * Get the items
     */
    public function items(): array
    {
        return $this->data;
    }

    /**
     * Get first item
     */
    public function first(): ?array
    {
        return $this->data[0] ?? null;
    }

    /**
     * Get last item
     */
    public function last(): ?array
    {
        return $this->data[count($this->data) - 1] ?? null;
    }

    /**
     * Check if there are items
     */
    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    /**
     * Check if there are items
     */
    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    /**
     * Get current page number
     */
    public function currentPage(): int
    {
        return $this->meta['current_page'] ?? 1;
    }

    /**
     * Get total pages
     */
    public function totalPages(): int
    {
        return $this->meta['last_page'] ?? 1;
    }

    /**
     * Get total items count
     */
    public function total(): int
    {
        return $this->meta['total'] ?? count($this->data);
    }

    /**
     * Get items per page
     */
    public function perPage(): int
    {
        return $this->meta['per_page'] ?? count($this->data);
    }

    /**
     * Check if has more pages
     */
    public function hasMorePages(): bool
    {
        return $this->currentPage() < $this->totalPages();
    }

    /**
     * Check if on first page
     */
    public function onFirstPage(): bool
    {
        return $this->currentPage() === 1;
    }

    /**
     * Check if on last page
     */
    public function onLastPage(): bool
    {
        return $this->currentPage() >= $this->totalPages();
    }

    /**
     * Get next page
     */
    public function nextPage(): ?self
    {
        if (!$this->hasMorePages()) {
            return null;
        }

        $params = array_merge($this->params, ['page' => $this->currentPage() + 1]);
        $response = $this->client->get($this->endpoint, $params);
        return new self($response, $this->client, $this->endpoint, $params);
    }

    /**
     * Get previous page
     */
    public function previousPage(): ?self
    {
        if ($this->onFirstPage()) {
            return null;
        }

        $params = array_merge($this->params, ['page' => $this->currentPage() - 1]);
        $response = $this->client->get($this->endpoint, $params);
        return new self($response, $this->client, $this->endpoint, $params);
    }

    /**
     * Get specific page
     */
    public function getPage(int $page): self
    {
        $params = array_merge($this->params, ['page' => $page]);
        $response = $this->client->get($this->endpoint, $params);
        return new self($response, $this->client, $this->endpoint, $params);
    }

    /**
     * Iterate through all pages
     * @return \Generator
     */
    public function all(): \Generator
    {
        $page = $this;

        while ($page !== null) {
            foreach ($page->items() as $item) {
                yield $item;
            }
            $page = $page->nextPage();
        }
    }

    /**
     * Map items
     */
    public function map(callable $callback): array
    {
        return array_map($callback, $this->data);
    }

    /**
     * Filter items
     */
    public function filter(callable $callback): array
    {
        return array_filter($this->data, $callback);
    }

    /**
     * Get meta information
     */
    public function meta(): array
    {
        return $this->meta;
    }

    /**
     * Get links
     */
    public function links(): array
    {
        return $this->links;
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'data' => $this->data,
            'meta' => $this->meta,
            'links' => $this->links,
        ];
    }

    // ArrayAccess implementation
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->data[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset === null) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->data[$offset]);
    }

    // Countable implementation
    public function count(): int
    {
        return count($this->data);
    }

    // Iterator implementation
    public function current(): mixed
    {
        return $this->data[$this->position];
    }

    public function key(): mixed
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return isset($this->data[$this->position]);
    }
}
