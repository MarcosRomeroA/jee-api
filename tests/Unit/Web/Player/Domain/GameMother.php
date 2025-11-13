<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Player\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Game;

final class GameMother
{
    public static function create(
        ?Uuid $id = null,
        ?string $name = null,
        ?string $description = null,
        ?int $minPlayers = null,
        ?int $maxPlayers = null
    ): Game {
        return new Game(
            $id ?? Uuid::random(),
            $name ?? 'League of Legends',
            $description ?? 'MOBA game',
            $minPlayers ?? 5,
            $maxPlayers ?? 5
        );
    }

    public static function random(): Game
    {
        return self::create();
    }

    public static function withName(string $name): Game
    {
        return self::create(name: $name);
    }
}

