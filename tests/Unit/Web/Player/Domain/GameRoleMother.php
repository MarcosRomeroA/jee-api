<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Player\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\GameRole;

final class GameRoleMother
{
    public static function create(?Uuid $id = null): GameRole
    {
        $gameRoleId = $id ?? Uuid::random();

        // Usar reflexión para crear un GameRole sin necesidad de la base de datos
        $gameRole = new class($gameRoleId) extends GameRole {
            public function __construct(Uuid $id)
            {
                $reflection = new \ReflectionClass(GameRole::class);
                $idProperty = $reflection->getProperty('id');
                $idProperty->setAccessible(true);
                $idProperty->setValue($this, $id);
            }
        };

        return $gameRole;
    }

    public static function random(): GameRole
    {
        return self::create();
    }
}

