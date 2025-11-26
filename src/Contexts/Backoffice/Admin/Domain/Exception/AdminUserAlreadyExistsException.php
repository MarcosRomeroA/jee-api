<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Admin\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class AdminUserAlreadyExistsException extends ApiException
{
    public function __construct(string $user)
    {
        parent::__construct(
            "Admin user <$user> already exists",
            'admin_user_already_exists_exception',
            Response::HTTP_CONFLICT
        );
    }
}
