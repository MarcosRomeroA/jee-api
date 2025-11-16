<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Team\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\Team;
use App\Contexts\Web\Team\Domain\TeamGame;
use App\Contexts\Web\Game\Domain\Game;

final class TeamGameMother
{
    public static function create(
        ?Uuid $id = null,
        ?Team $team = null,
        ?Game $game = null
    ): TeamGame {
        return new TeamGame(
            $id ?? Uuid::random(),
            $team ?? TeamMother::create(),
            $game ?? GameMother::random()
        );
    }

    public static function random(): TeamGame
    {
        return self::create();
    }

    public static function withTeam(Team $team): TeamGame
    {
        return self::create(null, $team);
    }

    public static function withGame(Game $game): TeamGame
    {
        return self::create(null, null, $game);
    }
}
