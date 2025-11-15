<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Player\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Rank;

final class RankMother
{
    public static function create(
        ?Uuid $id = null,
        ?string $name = null,
        ?string $description = null
    ): Rank {
        return new Rank(
            $id ?? Uuid::random(),
            $name ?? 'Gold',
            $description ?? 'Gold rank description'
        );
    }

    public static function random(): Rank
    {
        return self::create();
    }

    public static function withName(string $name): Rank
    {
        return self::create(name: $name);
    }
}
