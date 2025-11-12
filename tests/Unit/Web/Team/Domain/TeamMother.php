<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Team\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\Team;

final class TeamMother
{
    public static function create(
        ?Uuid $id = null,
        ?string $name = null,
        ?string $image = null
    ): Team {
        return new Team(
            $id ?? Uuid::random(),
            GameMother::random(),
            UserMother::random(),
            $name ?? 'Test Team',
            $image
        );
    }

    public static function random(): Team
    {
        return self::create();
    }

    public static function withName(string $name): Team
    {
        return self::create(null, $name);
    }

    public static function withImage(string $image): Team
    {
        return self::create(null, null, $image);
    }

    public static function withOwner(Uuid $ownerId): Team
    {
        return new Team(
            Uuid::random(),
            GameMother::random(),
            UserMother::create($ownerId),
            'Test Team',
            null
        );
    }
}

