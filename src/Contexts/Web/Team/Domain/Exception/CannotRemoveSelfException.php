<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class CannotRemoveSelfException extends ApiException
{
    public function __construct()
    {
        parent::__construct(
            "Cannot remove yourself from the team. Use the leave endpoint instead.",
            'cannot_remove_self',
            Response::HTTP_CONFLICT
        );
    }
}
