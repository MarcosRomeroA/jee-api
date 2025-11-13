<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Player\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Game;
use App\Contexts\Web\Game\Domain\GameRole;
use App\Contexts\Web\Game\Domain\Role;

final class GameRoleMother
{
    public static function create(
        ?Uuid $id = null,
        ?Role $role = null,
        ?Game $game = null
    ): GameRole {
        return new GameRole(
            $id ?? Uuid::random(),
            $role ?? RoleMother::random(),
            $game ?? GameMother::random()
        );
    }

    public static function random(): GameRole
    {
        return self::create();
    }
}

