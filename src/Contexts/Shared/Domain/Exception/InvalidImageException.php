<?php

declare(strict_types=1);

namespace App\Contexts\Shared\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class InvalidImageException extends ApiException
{
    public function __construct(string $reason)
    {
        parent::__construct(
            "Invalid image: $reason",
            'invalid_image_exception',
            Response::HTTP_BAD_REQUEST
        );
    }
}
