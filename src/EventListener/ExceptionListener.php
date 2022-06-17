<?php

namespace App\EventListener;

use App\Exceptions\ValidationFailedHttpException;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Throwable;

class ExceptionListener
{
    protected array $handableExceptions = [
        HttpExceptionInterface::class,
        ValidationFailedException::class,
        Exception::class,
    ];

    public function __construct(private KernelInterface $kernel)
    {
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if (!$this->exceptionDataCanBeAddedToResponse($exception)) {
            $response = $this->errorsResponse(new Exception('Oops, there was an internal error'));
            $event->setResponse($response);

            return;
        }
        $response = $this->errorsResponse($exception);

        $event->setResponse($response);

    }

    private function getExceptionStatusCode(Throwable $exception): int {
        if($exception instanceof HttpExceptionInterface) {
          $statusCode = $exception->getStatusCode(); 

          return $this->validHttpStatusCode($statusCode) ? $statusCode : Response::HTTP_INTERNAL_SERVER_ERROR;
        } 
        
        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    private function exceptionDataCanBeAddedToResponse(Throwable $exception): bool
    {
        return $this->isDebugEnvironment()
            || $exception instanceof HttpExceptionInterface
            || $exception instanceof ValidationFailedException;
    }

    private function errorsResponse(Throwable $exception): JsonResponse
    {
        return new JsonResponse(
            $this->errorsBody($exception),
        );
    }

    private function validHttpStatusCode(int $statusCode)
    {
        return $statusCode >= 100 || $statusCode <= 600;
    }

    private function errorsBody(Throwable $exception)
    {
        $errorsBody = [
            'errors' => [
                'status' => $statusCode = $this->getExceptionStatusCode($exception),
                'title' => Response::$statusTexts[$statusCode] ?? Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR],
                'code' => $exception->getCode(),
            ],
        ];

        if($exception instanceof ValidationFailedHttpException ||  $this->isDebugEnvironment()) {
            $errorsBody['errors']['details'] = $exception->getMessage();
        }

        if($this->isDebugEnvironment()) {
            $errorsBody['meta']['debug'] = true;
            $errorsBody['errors']['debug']['exception'] = $exception::class;
            $errorsBody['errors']['debug']['file'] = $exception->getFile();
            $errorsBody['errors']['debug']['line'] = $exception->getLine();
            $errorsBody['errors']['debug']['stacktrace'] = $exception->getTraceAsString();
        }

        return $errorsBody;
    }

    private function isDebugEnvironment(): bool {
        return $this->kernel->getEnvironment() === 'dev';
    }
}