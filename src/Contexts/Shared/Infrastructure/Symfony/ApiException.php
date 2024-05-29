<?php declare(strict_types=1);

namespace App\Contexts\Shared\Infrastructure\Symfony;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class ApiException extends HttpException
{
    public function __construct(
        string $message = "unexpected_error",
        int $statusCode  = Response::HTTP_BAD_REQUEST,
        \Exception $previous = null,
        array $headers = [],
        int $code = 0
    )
    {
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }
}