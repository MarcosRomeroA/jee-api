<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class RosterNotFoundException extends ApiException
{
    public function __construct(string $rosterId)
    {
        parent::__construct(
            "Roster with id <$rosterId> not found",
            'roster_not_found_exception',
            Response::HTTP_NOT_FOUND
        );
    }
}
