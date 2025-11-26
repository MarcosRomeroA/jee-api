<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Admin\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class AdminNotFoundException extends ApiException
{
    public function __construct(string $adminId)
    {
        parent::__construct(
            "Admin with id <$adminId> not found",
            'admin_not_found_exception',
            Response::HTTP_NOT_FOUND
        );
    }
}
