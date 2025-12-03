<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class PlayerAlreadyInRosterException extends ApiException
{
    public function __construct(string $playerId, string $rosterId)
    {
        parent::__construct(
            "Player <$playerId> is already in roster <$rosterId>",
            'player_already_in_roster_exception',
            Response::HTTP_CONFLICT
        );
    }
}
