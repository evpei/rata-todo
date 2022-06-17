<?php

namespace App\Exceptions;

use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationFailedHttpException extends RuntimeException implements HttpExceptionInterface
{
    public function __construct(string|ConstraintViolationListInterface $violations)
    {
        $this->violations = $violations;

        parent::__construct(is_string($violations) ? $violations : $this->joinErrorMessages());
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_UNPROCESSABLE_ENTITY;
    }

    /**
     * Returns response headers.
     */
    public function getHeaders(): array
    {
        return [];
    }

    protected function joinErrorMessages(): string
    {
        $errorMessages = [];

        foreach ($this->violations as $violation) {
            $errorMessages[] = $violation->getMessage();
        }

        return implode(' ', $errorMessages);
    }
}
