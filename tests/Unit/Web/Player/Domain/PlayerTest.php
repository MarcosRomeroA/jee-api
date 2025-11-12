<?php declare(strict_types=1);

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
        $username = 'ProGamer123';

        $player = new Player(
            $id,
            UserMother::random(),
            GameRoleMother::random(),
            GameRankMother::random(),
            new UsernameValue($username),
            false
        );

        $this->assertEquals($id, $player->id());
        $this->assertEquals($username, $player->username()->value());
        $this->assertFalse($player->verified());
    }

    public function testItShouldUpdatePlayer(): void
    {
        $player = PlayerMother::create();
        $newUsername = new UsernameValue('UpdatedGamer456');
        $newGameRole = GameRoleMother::random();
        $newGameRank = GameRankMother::random();

        $player->update($newUsername, $newGameRole, $newGameRank);

        $this->assertEquals($newUsername->value(), $player->username()->value());
        $this->assertEquals($newGameRole, $player->gameRole());
        $this->assertEquals($newGameRank, $player->gameRank());
    }

    public function testItShouldVerifyPlayer(): void
    {
        $player = PlayerMother::create();

        $player->verify();

        $this->assertTrue($player->verified());
    }
}

