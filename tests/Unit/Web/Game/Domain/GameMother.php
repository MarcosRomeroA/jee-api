<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Game\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Game;
use App\Tests\Unit\Shared\Domain\ValueObject\UuidMother;

final class GameMother
{
    public static function create(
        ?Uuid $id = null,
        ?string $name = null,
        ?string $description = null,
        ?int $minPlayersQuantity = null,
        ?int $maxPlayersQuantity = null
    ): Game {
        return new Game(
            $id ?? UuidMother::create(),
            $name ?? 'League of Legends',
            $description ?? 'MOBA game',
            $minPlayersQuantity ?? 5,
            $maxPlayersQuantity ?? 5
        );
    }

    public static function withId(string $id): Game
    {
        return self::create(id: new Uuid($id));
    }

    public static function random(): Game
    {
        return self::create();
    }
}

