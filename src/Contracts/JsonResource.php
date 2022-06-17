<?php

namespace App\Contracts;

interface JsonResource {
    public function toArray(): array;
}