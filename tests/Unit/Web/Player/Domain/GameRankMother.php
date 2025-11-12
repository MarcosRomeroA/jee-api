<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Player\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\GameRank;

final class GameRankMother
{
    public static function create(?Uuid $id = null): GameRank
    {
        $gameRankId = $id ?? Uuid::random();

        // Usar reflexión para crear un GameRank sin necesidad de la base de datos
        $gameRank = new class($gameRankId) extends GameRank {
            public function __construct(Uuid $id)
            {
                $reflection = new \ReflectionClass(GameRank::class);
                $idProperty = $reflection->getProperty('id');
                $idProperty->setAccessible(true);
                $idProperty->setValue($this, $id);
            }
        };

        return $gameRank;
    }

    public static function random(): GameRank
    {
        return self::create();
    }
}

