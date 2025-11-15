<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Game\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Rank;
use App\Tests\Unit\Shared\Domain\ValueObject\UuidMother;

final class RankMother
{
    public static function create(
        ?Uuid $id = null,
        ?string $name = null,
        ?string $description = null
    ): Rank {
        return new Rank(
            $id ?? UuidMother::create(),
            $name ?? 'Gold',
            $description ?? 'Gold rank'
        );
    }

    public static function random(): Rank
    {
        return self::create();
    }
}
