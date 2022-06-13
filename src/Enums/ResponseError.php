<?php

namespace App\Enums;

use App\Contracts\Error;

enum ResponseError implements Error {

    case HTTP_NOT_FOUND;
    case HTTP_BAD_REQUEST;
    
    public function getMessage(): string
    {
        return match($this) {
            ResponseError::HTTP_NOT_FOUND => 'task not found',
            ResponseError::HTTP_BAD_REQUEST => 'bad request',
            default => 'Oops, something went wrong.'
        };
    }

    public function getStatusCode(): int
    {
        return match($this) {
            ResponseError::HTTP_NOT_FOUND => 404,
            ResponseError::HTTP_BAD_REQUEST => 400,
            default => 500,
        };
    }
}