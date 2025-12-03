<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class GameNotInTeamException extends ApiException
{
    public function __construct(string $gameId, string $teamId)
    {
        parent::__construct(
            "Game <$gameId> is not associated with team <$teamId>",
            'game_not_in_team_exception',
            Response::HTTP_CONFLICT
        );
    }
}
