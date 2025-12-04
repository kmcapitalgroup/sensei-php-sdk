<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK\Exceptions;

/**
 * Thrown for 429 rate limit errors
 */
class RateLimitException extends SenseiPartnerException
{
    private int $retryAfter;

    public function __construct(
        string $message = 'Rate limit exceeded',
        int $retryAfter = 60,
        ?\Exception $previous = null
    ) {
        parent::__construct($message, 429, $previous);
        $this->retryAfter = $retryAfter;
    }

    /**
     * Get seconds to wait before retrying
     */
    public function getRetryAfter(): int
    {
        return $this->retryAfter;
    }
}
