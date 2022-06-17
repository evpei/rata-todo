<?php

namespace App\Controller;

use App\Contracts\JsonResource;
use App\Contracts\TokenAuthenticatedController;
use App\Exceptions\ValidationFailedHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Throwable;

abstract class ApiController extends AbstractController implements TokenAuthenticatedController
{
    protected function formattedJsonResponse(array|JsonResource $data = [], int $statusCode = Response::HTTP_OK): JsonResponse {
        $data = $data instanceof JsonResource ? $data->toArray() : $data;

        return $this->json(['data' => $data], $statusCode);
    }

    protected function createAccessDeniedHttpException(string $message = 'Access Denied.', ?Throwable $previous = null): AccessDeniedHttpException
    {
        return new AccessDeniedHttpException($message, $previous);
    }

    protected function throwValidationException(string $message): never
    {
        throw new ValidationFailedHttpException($message);
    }
}
