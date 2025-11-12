<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Game\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Game;
use App\Contexts\Web\Game\Domain\GameRole;
use App\Contexts\Web\Game\Domain\Role;
use App\Tests\Unit\Shared\Domain\ValueObject\UuidMother;

final class GameRoleMother
{
    public static function create(
        ?Uuid $id = null,
        ?Role $role = null,
        ?Game $game = null
    ): GameRole {
        return new GameRole(
            $id ?? UuidMother::create(),
            $role ?? RoleMother::create(),
            $game ?? GameMother::create()
        );
    }

    public static function random(): GameRole
    {
        return self::create();
    }
}

