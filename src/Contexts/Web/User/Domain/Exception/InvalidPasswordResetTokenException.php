<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class InvalidPasswordResetTokenException extends ApiException
{
    public function __construct()
    {
        parent::__construct(
            'Invalid or expired password reset token',
            'invalid_password_reset_token',
            Response::HTTP_BAD_REQUEST
        );
    }
}
