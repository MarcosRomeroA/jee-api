<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class CannotRemoveCreatorException extends ApiException
{
    public function __construct(string $teamId)
    {
        parent::__construct(
            "Cannot remove the creator from team <$teamId>.",
            'cannot_remove_creator',
            Response::HTTP_CONFLICT
        );
    }
}
