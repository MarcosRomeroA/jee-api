<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Player\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Player\Domain\Player;
use App\Contexts\Web\Player\Domain\ValueObject\UsernameValue;

final class PlayerMother
{
    public static function create(
        ?Uuid $id = null,
        ?string $username = null,
        ?bool $verified = null
    ): Player {
        return new Player(
            $id ?? Uuid::random(),
            UserMother::random(),
            GameRoleMother::random(),
            GameRankMother::random(),
            new UsernameValue($username ?? 'TestPlayer'),
            $verified ?? false
        );
    }

    public static function random(): Player
    {
        return self::create();
    }

    public static function withUsername(string $username): Player
    {
        return self::create(null, $username);
    }

    public static function verified(): Player
    {
        return self::create(null, null, true);
    }
}

