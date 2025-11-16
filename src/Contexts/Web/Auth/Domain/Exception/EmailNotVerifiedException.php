<?php

declare(strict_types=1);

namespace App\Contexts\Web\Auth\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class EmailNotVerifiedException extends ApiException
{
    public function __construct()
    {
        parent::__construct(
            "Email address has not been verified. Please check your email and click the confirmation link.",
            "email_not_verified",
            Response::HTTP_FORBIDDEN
        );
    }
}
