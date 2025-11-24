<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class CannotLeaveAsOwnerException extends ApiException
{
    public function __construct(string $teamId)
    {
        parent::__construct(
            "Cannot leave team <$teamId> as owner. Transfer ownership first.",
            'cannot_leave_as_owner',
            Response::HTTP_CONFLICT
        );
    }
}
