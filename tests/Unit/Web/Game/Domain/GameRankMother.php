<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Game\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Game;
use App\Contexts\Web\Game\Domain\GameRank;
use App\Tests\Unit\Shared\Domain\ValueObject\UuidMother;

final class GameRankMother
{
    public static function create(
        ?Uuid $id = null,
        ?Game $game = null,
        ?Rank $rank = null,
        ?int $level = null,
    ): GameRank {
        return new GameRank(
            $id ?? UuidMother::create(),
            $game ?? GameMother::create(),
            $rank ?? RankMother::create(),
            $level ?? 4,
        );
    }

    public static function random(): GameRank
    {
        return self::create();
    }
}
