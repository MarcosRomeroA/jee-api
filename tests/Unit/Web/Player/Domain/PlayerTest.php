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
            new UsernameValue($username),
            GameRankMother::random(),
            false
        );

        $this->assertEquals($id, $player->id());
        $this->assertEquals($username, $player->username()->value());
        $this->assertFalse($player->verified());
        $this->assertCount(0, $player->playerRoles()); // No roles initially
    }

    public function testItShouldUpdatePlayer(): void
    {
        $player = PlayerMother::create();
        $newUsername = new UsernameValue('UpdatedGamer456');
        $newGameRank = GameRankMother::random();

        $player->update($newUsername, $newGameRank);

        $this->assertEquals($newUsername->value(), $player->username()->value());
        $this->assertEquals($newGameRank, $player->gameRank());
    }

    public function testItShouldVerifyPlayer(): void
    {
        $player = PlayerMother::create();

        $player->verify();

        $this->assertTrue($player->verified());
    }

    public function testItShouldAddRole(): void
    {
        $player = PlayerMother::create();
        $gameRole = GameRoleMother::random();

        $player->addRole($gameRole);

        $this->assertCount(2, $player->playerRoles()); // 1 from mother + 1 added
        $this->assertContains($gameRole, $player->gameRoles());
    }

    public function testItShouldNotAddDuplicateRole(): void
    {
        $player = PlayerMother::create();
        $gameRole = GameRoleMother::random();

        $player->addRole($gameRole);
        $player->addRole($gameRole); // Try to add again

        $this->assertCount(2, $player->playerRoles()); // Still only 2 roles
    }

    public function testItShouldRemoveRole(): void
    {
        $player = PlayerMother::create();
        $gameRole = GameRoleMother::random();
        $player->addRole($gameRole);

        $this->assertCount(2, $player->playerRoles());

        $player->removeRole($gameRole);

        $this->assertCount(1, $player->playerRoles());
        $this->assertNotContains($gameRole, $player->gameRoles());
    }

    public function testItShouldClearRoles(): void
    {
        $player = PlayerMother::create();
        $player->addRole(GameRoleMother::random());
        $player->addRole(GameRoleMother::random());

        $player->clearRoles();

        $this->assertCount(0, $player->playerRoles());
    }
}

