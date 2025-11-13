<?php declare(strict_types=1);

namespace App\Contexts\Shared\Infrastructure\Symfony\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

final class ValidationException extends HttpException
{
    public function __construct(private readonly array $errors)
    {
        parent::__construct(422, 'Validation failed');
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}

