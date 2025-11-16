<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Player\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Game;
use App\Contexts\Web\Game\Domain\GameRank;
use App\Contexts\Web\Game\Domain\GameRole;
use App\Contexts\Web\Game\Domain\Role;
use App\Contexts\Web\Player\Domain\Player;
use App\Contexts\Web\Player\Domain\ValueObject\UsernameValue;
use App\Contexts\Web\User\Domain\User;

final class PlayerMother
{
    public static function create(
        ?Uuid $id = null,
        ?User $user = null,
        ?array $gameRoles = null,
        ?GameRank $gameRank = null,
        ?string $username = null,
        ?bool $verified = null
    ): Player {
        return new Player(
            $id ?? Uuid::random(),
            $user ?? UserMother::random(),
            $gameRoles ?? [GameRoleMother::random()],
            $gameRank ?? GameRankMother::random(),
            new UsernameValue($username ?? 'testplayer' . rand(1, 1000)),
            $verified ?? false
        );
    }

    public static function random(): Player
    {
        return self::create();
    }

    public static function withUsername(string $username): Player
    {
        return self::create(username: $username);
    }

    public static function verified(): Player
    {
        return self::create(verified: true);
    }

    public static function withUser(User $user): Player
    {
        return self::create(user: $user);
    }
}
