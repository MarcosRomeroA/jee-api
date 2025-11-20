<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Conversation\Domain;

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
        ?string $username = null,
        ?string $email = null,
        ?string $firstname = null,
        ?string $lastname = null
    ): User {
        return User::create(
            $id ?? Uuid::random(),
            new FirstnameValue($firstname ?? 'John'),
            new LastnameValue($lastname ?? 'Doe'),
            new UsernameValue($username ?? 'testuser' . rand(1, 999)),
            new EmailValue($email ?? 'test' . rand(1, 999) . '@example.com'),
            new PasswordValue('password123'),
            new ProfileImageValue(''),
            null
        );
    }

    public static function random(): User
    {
        return self::create();
    }

    public static function withUsername(string $username): User
    {
        return self::create(username: $username);
    }
}
