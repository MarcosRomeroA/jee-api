<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Player\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Game;
use App\Contexts\Web\Game\Domain\GameRank;

final class GameRankMother
{
    public static function create(
        ?Uuid $id = null,
        ?Game $game = null,
        ?string $name = null,
        ?int $level = null
    ): GameRank {
        return new GameRank(
            $id ?? Uuid::random(),
            $game ?? GameMother::random(),
            $name ?? 'Gold',
            $level ?? 5
        );
    }

    public static function random(): GameRank
    {
        return self::create();
    }

    public static function withLevel(int $level): GameRank
    {
        return self::create(level: $level);
    }
}

