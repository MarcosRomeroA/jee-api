<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Player\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\User;

final class UserMother
{
    public static function create(
        ?Uuid $id = null,
        ?string $email = null,
        ?string $name = null
    ): User {
        // Usar reflexión para crear un User sin necesidad de todos los datos
        $user = new class($id ?? Uuid::random()) extends User {
            public function __construct(Uuid $id)
            {
                $reflection = new \ReflectionClass(User::class);
                $idProperty = $reflection->getProperty('id');
                $idProperty->setAccessible(true);
                $idProperty->setValue($this, $id);
            }
        };

        return $user;
    }

    public static function random(): User
    {
        return self::create();
    }
}

