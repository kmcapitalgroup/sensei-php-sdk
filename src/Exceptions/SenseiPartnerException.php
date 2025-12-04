<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK\Exceptions;

use Exception;
use Psr\Http\Message\ResponseInterface;

/**
 * Base exception for all Sensei Partner SDK errors
 */
class SenseiPartnerException extends Exception
{
    protected ?ResponseInterface $response = null;
    protected array $responseBody = [];

    public function __construct(
        string $message = '',
        int $code = 0,
        ?Exception $previous = null,
        ?ResponseInterface $response = null,
        array $responseBody = []
    ) {
        parent::__construct($message, $code, $previous);
        $this->response = $response;
        $this->responseBody = $responseBody;
    }

    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    public function getResponseBody(): array
    {
        return $this->responseBody;
    }

    public function getErrorCode(): ?string
    {
        return $this->responseBody['error'] ?? null;
    }

    public function getErrorMessage(): ?string
    {
        return $this->responseBody['message'] ?? $this->getMessage();
    }

    public function getErrors(): array
    {
        return $this->responseBody['errors'] ?? [];
    }

    public function hasFieldError(string $field): bool
    {
        return isset($this->responseBody['errors'][$field]);
    }

    public function getFieldErrors(string $field): array
    {
        return $this->responseBody['errors'][$field] ?? [];
    }
}
