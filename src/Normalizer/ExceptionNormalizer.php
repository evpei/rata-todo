<?php

namespace App\Normalizer;

use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ExceptionNormalizer implements NormalizerInterface
{
    public function __construct(private KernelInterface $kernel)
    {
    }
    /**
     * Normalize non-html exceptions
     *
     * @param \Throwable $exception
     * @param string|null $format
     * @param array $context
     * @return void
     */
    public function normalize($exception, string $format = null, array $context = [])
    {
        return $this->responseBody($exception);
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof FlattenException;
    }
    
    /**
     * Returns the properly formatted Error responseBody
     *
     * @param \Throwable $exception
     * @return array
     */
    private function responseBody($exception): array
    {
        $body = ['error' => [
            'message' => $this->getErrorMessage($exception),
            'code' => $exception->getStatusCode(),
        ]];

        if ($this->applicationInDebugMode()) {
            $body['debug'] = true;
        }

        return $body;
    }

    private function getErrorMessage($exception): string
    {
        if ($this->sendExceptionMessageInResponse($exception)) {
            return $exception->getMessage();
        }

        return 'Oops, something went wrong. 💥';
    }

    private function applicationInDebugMode(): bool
    {
        return $this->kernel->isDebug();
    }

    /**
     * Determines whether the exception message should be send in the response
     *
     * @param \Throwable $exception
     * @return boolean
     */
    private function sendExceptionMessageInResponse($exception): bool
    {
        return $this->applicationInDebugMode() || $exception instanceof HttpExceptionInterface;
    }
}
