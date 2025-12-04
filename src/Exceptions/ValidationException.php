<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK\Exceptions;

/**
 * Thrown for 422 validation errors
 */
class ValidationException extends SenseiPartnerException
{
    /**
     * Get all validation error messages as flat array
     */
    public function getAllMessages(): array
    {
        $messages = [];
        foreach ($this->getErrors() as $field => $fieldErrors) {
            foreach ((array) $fieldErrors as $error) {
                $messages[] = $error;
            }
        }
        return $messages;
    }

    /**
     * Get first validation error message
     */
    public function getFirstMessage(): ?string
    {
        $messages = $this->getAllMessages();
        return $messages[0] ?? null;
    }
}
