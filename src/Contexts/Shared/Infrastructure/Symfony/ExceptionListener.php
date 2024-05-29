<?php declare(strict_types=1);

namespace App\Contexts\Shared\Infrastructure\Symfony;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // Default values
        $statusCode = 500;
        $message = 'An unexpected error occurred';
        $errorCode = 'unexpected_error';

        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
            $message = $exception->getMessage();
            $errorCode = $this->generateErrorCode($exception);
        }

        $response = new JsonResponse([
            'status' => $statusCode,
            'message' => $message,
            'code' => $errorCode,
        ]);

        $response->setStatusCode($statusCode);

        $event->setResponse($response);
    }

    private function generateErrorCode(\Throwable $exception): string
    {
        $className = (new \ReflectionClass($exception))->getShortName();
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $className));
    }
}