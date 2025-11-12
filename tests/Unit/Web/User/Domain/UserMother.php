<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\User\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\User;

final class UserMother
{
    public static function create(?Uuid $id = null): User
    {
        $userId = $id ?? Uuid::random();

        $user = new class($userId) extends User {
            private Uuid $id;

            public function __construct(Uuid $id)
            {
                $this->id = $id;

                $reflection = new \ReflectionClass(User::class);
                $idProperty = $reflection->getProperty('id');
                $idProperty->setAccessible(true);
                $idProperty->setValue($this, $id);
            }

            public function id(): Uuid
            {
                return $this->id;
            }
        };

        return $user;
    }

    public static function random(): User
    {
        return self::create();
    }
}

