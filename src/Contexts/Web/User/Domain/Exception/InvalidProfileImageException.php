<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class InvalidProfileImageException extends ApiException
{
    public function __construct(string $reason)
    {
        parent::__construct(
            "Invalid profile image: $reason",
            'invalid_profile_image',
            Response::HTTP_BAD_REQUEST
        );
    }
}
