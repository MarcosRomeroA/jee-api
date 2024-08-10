<?php declare(strict_types=1);

namespace App\Contexts\Shared\Infrastructure\Symfony;

use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class ApiException extends HttpException
{
    private string $uniqueCode;

    public function __construct(
        string $message,
        string $uniqueCode,
        int $statusCode,
    )
    {
        $this->uniqueCode = $uniqueCode;
        parent::__construct($statusCode, $message);
    }

    public function getUniqueCode(): string
    {
        return $this->uniqueCode;
    }
}