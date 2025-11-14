<?php declare(strict_types=1);

namespace App\Contexts\Shared\Infrastructure\Symfony\EventListener;

use App\Contexts\Shared\Infrastructure\Symfony\Exception\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

final class ValidationExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof ValidationException) {
            return;
        }

        $response = new JsonResponse(
            ["errors" => $exception->getErrors()],
            422,
        );

        $event->setResponse($response);
        $event->stopPropagation();
    }
}
