<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class RosterLeaderAlreadyExistsException extends ApiException
{
    public function __construct(string $rosterId)
    {
        parent::__construct(
            "Roster <$rosterId> already has a leader",
            'roster_leader_already_exists_exception',
            Response::HTTP_CONFLICT
        );
    }
}
