<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Notification\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\User\Domain\ValueObject\EmailValue;
use App\Contexts\Web\User\Domain\ValueObject\FirstnameValue;
use App\Contexts\Web\User\Domain\ValueObject\LastnameValue;
use App\Contexts\Web\User\Domain\ValueObject\PasswordValue;
use App\Contexts\Web\User\Domain\ValueObject\ProfileImageValue;
use App\Contexts\Web\User\Domain\ValueObject\UsernameValue;

final class UserMother
{
    public static function create(
        ?Uuid $id = null,
        ?string $username = null
    ): User {
        return User::create(
            $id ?? Uuid::random(),
            new FirstnameValue('John'),
            new LastnameValue('Doe'),
            new UsernameValue($username ?? 'testuser' . rand(1, 999)),
            new EmailValue('test' . rand(1, 999) . '@example.com'),
            new PasswordValue('password123'),
            new ProfileImageValue(''),
            null
        );
    }

    public static function random(): User
    {
        return self::create();
    }
}
