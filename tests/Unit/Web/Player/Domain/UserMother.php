<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Player\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\User\Domain\ValueObject\EmailValue;
use App\Contexts\Web\User\Domain\ValueObject\FirstnameValue;
use App\Contexts\Web\User\Domain\ValueObject\LastnameValue;
use App\Contexts\Web\User\Domain\ValueObject\PasswordValue;
use App\Contexts\Web\User\Domain\ValueObject\UsernameValue;

final class UserMother
{
    public static function create(
        ?Uuid $id = null,
        ?string $firstname = null,
        ?string $lastname = null,
        ?string $username = null,
        ?string $email = null,
        ?string $password = null
    ): User {
        return new User(
            $id ?? Uuid::random(),
            new FirstnameValue($firstname ?? 'John'),
            new LastnameValue($lastname ?? 'Doe'),
            new UsernameValue($username ?? 'testuser' . rand(1, 1000)),
            new EmailValue($email ?? 'test' . rand(1, 1000) . '@example.com'),
            new PasswordValue($password ?? password_hash('password123', PASSWORD_BCRYPT))
        );
    }

    public static function random(): User
    {
        return self::create();
    }

    public static function withId(Uuid $id): User
    {
        return self::create($id);
    }

    public static function withUsername(string $username): User
    {
        return self::create(username: $username);
    }
}

