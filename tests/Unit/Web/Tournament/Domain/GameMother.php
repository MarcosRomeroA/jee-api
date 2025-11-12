<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Tournament\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Game;

final class GameMother
{
    public static function create(?Uuid $id = null, ?string $name = null): Game
    {
        $gameId = $id ?? Uuid::random();
        $gameName = $name ?? 'Test Game';

        $game = new class($gameId, $gameName) extends Game {
            public function __construct(Uuid $id, string $name)
            {
                $reflection = new \ReflectionClass(Game::class);

                $idProperty = $reflection->getProperty('id');
                $idProperty->setAccessible(true);
                $idProperty->setValue($this, $id);

                $nameProperty = $reflection->getProperty('name');
                $nameProperty->setAccessible(true);
                $nameProperty->setValue($this, $name);
            }
        };

        return $game;
    }

    public static function random(): Game
    {
        return self::create();
    }
}

