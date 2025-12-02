<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Player\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Player\Domain\Player;
use App\Contexts\Web\Player\Domain\ValueObject\GameAccountDataValue;
use PHPUnit\Framework\TestCase;

final class PlayerTest extends TestCase
{
    public function testItShouldCreateAPlayer(): void
    {
        $id = Uuid::random();
        $user = UserMother::random();
        $game = GameMother::random();
        $gameRole = GameRoleMother::random();
        $gameRoles = [$gameRole];
        $accountData = [
            'region' => 'las',
            'username' => 'RiotPlayer',
            'tag' => '1234',
        ];

        $player = Player::create(
            $id,
            $user,
            $game,
            $gameRoles,
            new GameAccountDataValue($accountData),
            false
        );

        $this->assertEquals($id, $player->id());
        $this->assertEquals($user, $player->user());
        $this->assertEquals($game, $player->game());
        $this->assertCount(1, $player->gameRoles());
        $this->assertEquals($gameRole, $player->gameRoles()[0]);
        $this->assertNull($player->gameRank());
        $this->assertEquals('RiotPlayer', $player->username());
        $this->assertEquals($accountData, $player->accountData()->value());
        $this->assertFalse($player->verified());
    }

    public function testItShouldUpdatePlayer(): void
    {
        $player = PlayerMother::create();
        $newGameRole = GameRoleMother::random();
        $newGameRoles = [$newGameRole];
        $newAccountData = new GameAccountDataValue([
            'region' => 'las',
            'username' => 'UpdatedRiot',
            'tag' => '5678',
        ]);

        $player->update($newGameRoles, $newAccountData);

        $this->assertEquals('UpdatedRiot', $player->username());
        $this->assertCount(1, $player->gameRoles());
        $this->assertEquals($newGameRole, $player->gameRoles()[0]);
        $this->assertEquals($newAccountData->value(), $player->accountData()->value());
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
