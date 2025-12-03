<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class PlayerNotInTeamException extends ApiException
{
    public function __construct(string $playerId, string $teamId)
    {
        parent::__construct(
            "Player <$playerId> does not belong to a user who is a member of team <$teamId>",
            'player_not_in_team_exception',
            Response::HTTP_CONFLICT
        );
    }
}
