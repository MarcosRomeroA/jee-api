<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class PasswordConfirmationMismatchException extends ApiException
{
    public function __construct()
    {
        parent::__construct(
            'Password and password confirmation do not match',
            'password_confirmation_mismatch',
            Response::HTTP_BAD_REQUEST
        );
    }
}
