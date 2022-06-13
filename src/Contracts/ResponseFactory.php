<?php 

namespace App\Contracts;

use App\Contracts\DTO;
use App\Contracts\Error;
use Symfony\Component\HttpFoundation\Response;

interface ResponseFactory {
    public function success(DTO $dto): Response;

    public function error(Error $error): Response;
}