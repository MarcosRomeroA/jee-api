<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Player\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Game;
use App\Contexts\Web\Player\Domain\Player;
use App\Contexts\Web\Player\Domain\ValueObject\GameAccountDataValue;
use App\Contexts\Web\User\Domain\User;

final class PlayerMother
{
    public static function create(
        ?Uuid $id = null,
        ?User $user = null,
        ?Game $game = null,
        ?array $gameRoles = null,
        ?array $accountData = null,
        ?bool $verified = null
    ): Player {
        return Player::create(
            $id ?? Uuid::random(),
            $user ?? UserMother::random(),
            $game ?? GameMother::random(),
            $gameRoles ?? [GameRoleMother::random()],
            new GameAccountDataValue($accountData ?? [
                'region' => 'las',
                'username' => 'TestRiot',
                'tag' => '1234',
            ]),
            $verified ?? false
        );
    }

    public static function random(): Player
    {
        return self::create();
    }

    public static function withAccountData(array $accountData): Player
    {
        return self::create(accountData: $accountData);
    }

    public static function verified(): Player
    {
        return self::create(verified: true);
    }

    public static function withUser(User $user): Player
    {
        return self::create(user: $user);
    }

    public static function withGame(Game $game): Player
    {
        return self::create(game: $game);
    }
}
