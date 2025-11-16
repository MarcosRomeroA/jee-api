<?php

declare(strict_types=1);

namespace App\Contexts\Shared\Infrastructure\Symfony;

use App\Contexts\Shared\Domain\Exception\UnauthorizedException;
use App\Contexts\Shared\Domain\Exception\ValidationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

class ExceptionListener
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // TODO: Improve this to handle async commands and queries
        if ($exception instanceof HandlerFailedException) {
            $exception = $exception->getPrevious();
        }

        $statusCode = 500;
        $message = "An unexpected error occurred";
        $errorCode = "unexpected_error";
        $errors = [];

        $this->logger->critical($exception->getMessage());

        if ($exception instanceof ValidationException) {
            $statusCode = $exception->getStatusCode();
            $message = $exception->getMessage();
            $errorCode = $exception->getUniqueCode();
            $errors = $exception->getErrors();
        } elseif ($exception instanceof ApiException) {
            $statusCode = $exception->getStatusCode();
            $message = $exception->getMessage();
            $errorCode = $exception->getUniqueCode();
        }

        $result = [
            "status" => $statusCode,
            "message" => $message,
            "code" => $errorCode,
        ];

        if (!empty($errors)) {
            $result["errors"] = $errors;
        }

        if ($_ENV["APP_ENV"] === "dev") {
            $result["dev_error_message"] = $exception->getMessage();
        }

        $response = new JsonResponse($result);

        $response->setStatusCode($statusCode);

        $event->setResponse($response);
    }
}
