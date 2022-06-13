<?php

namespace App\Exceptions;

use App\Contracts\DTO;
use Exception;

class WrongDTOException extends Exception {
    public static function throw(string $expected, DTO $actually) {
        throw new self(sprintf('Wrong DTO Provided. Expected "%s" but got "%s"', $expected, $actually::class));
    }
}