<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class RosterPlayerNotFoundException extends ApiException
{
    public function __construct(string $rosterPlayerId)
    {
        parent::__construct(
            "Roster player with id <$rosterPlayerId> not found",
            'roster_player_not_found_exception',
            Response::HTTP_NOT_FOUND
        );
    }
}
