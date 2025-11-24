<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class UserNotMemberException extends ApiException
{
    public function __construct(string $teamId, string $userId)
    {
        parent::__construct(
            "User <$userId> is not a member of team <$teamId>",
            'user_not_member',
            Response::HTTP_NOT_FOUND
        );
    }
}
