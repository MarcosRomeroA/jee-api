<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Player\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Player\Domain\Player;
use App\Contexts\Web\Player\Domain\ValueObject\UsernameValue;
use PHPUnit\Framework\TestCase;

final class PlayerTest extends TestCase
{
    public function testItShouldCreateAPlayer(): void
    {
        $id = Uuid::random();
        $user = UserMother::random();
        $gameRole = GameRoleMother::random();
        $gameRoles = [$gameRole];
        $gameRank = GameRankMother::random();
        $username = 'ProGamer123';

        $player = new Player(
            $id,
            $user,
            $gameRoles,
            $gameRank,
            new UsernameValue($username),
            false
        );

        $this->assertEquals($id, $player->id());
        $this->assertEquals($user, $player->user());
        $this->assertCount(1, $player->gameRoles());
        $this->assertEquals($gameRole, $player->gameRoles()[0]);
        $this->assertEquals($gameRank, $player->gameRank());
        $this->assertEquals($username, $player->username()->value());
        $this->assertFalse($player->verified());
    }

    public function testItShouldUpdatePlayer(): void
    {
        $player = PlayerMother::create();
        $newUsername = new UsernameValue('UpdatedGamer456');
        $newGameRole = GameRoleMother::random();
        $newGameRoles = [$newGameRole];
        $newGameRank = GameRankMother::random();

        $player->update($newUsername, $newGameRoles, $newGameRank);

        $this->assertEquals($newUsername->value(), $player->username()->value());
        $this->assertCount(1, $player->gameRoles());
        $this->assertEquals($newGameRole, $player->gameRoles()[0]);
        $this->assertEquals($newGameRank, $player->gameRank());
    }

    public function testItShouldVerifyPlayer(): void
    {
        $player = PlayerMother::create(verified: false);

        $this->assertFalse($player->verified());

        $player->verify();

        $this->assertTrue($player->verified());
    }

    public function testItShouldCreateVerifiedPlayer(): void
    {
        $player = PlayerMother::verified();

        $this->assertTrue($player->verified());
    }
}
