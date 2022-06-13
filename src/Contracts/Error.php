<?php

namespace App\Contracts;


interface Error {
    public function getMessage(): string;
    public function getStatusCode(): int;
}