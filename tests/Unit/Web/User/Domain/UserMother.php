<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\User\Domain;

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
    public static function random(): User
    {
        return self::create();
    }

    public static function create(
        ?Uuid $id = null,
        ?string $firstname = null,
        ?string $lastname = null,
        ?string $username = null,
        ?string $email = null,
        ?string $password = null,
        ?string $profileImage = null,
        ?string $description = null,
        ?\DateTimeImmutable $createdAt = null
    ): User {
        $user = User::create(
            $id ?? Uuid::random(),
            new FirstnameValue($firstname ?? 'John'),
            new LastnameValue($lastname ?? 'Doe'),
            new UsernameValue($username ?? 'johndoe'),
            new EmailValue($email ?? 'john@example.com'),
            new PasswordValue($password ?? password_hash('password123', PASSWORD_BCRYPT))
        );

        if ($profileImage !== null) {
            $reflection = new \ReflectionClass($user);
            $property = $reflection->getProperty('profileImage');
            $property->setAccessible(true);
            $property->setValue($user, new ProfileImageValue($profileImage));
        }

        if ($description !== null) {
            $reflection = new \ReflectionClass($user);
            $property = $reflection->getProperty('description');
            $property->setAccessible(true);
            $property->setValue($user, $description);
        }

        if ($createdAt !== null) {
            $reflection = new \ReflectionClass($user);
            $property = $reflection->getProperty('createdAt');
            $property->setAccessible(true);
            $property->setValue($user, $createdAt);
        }

        return $user;
    }

    public static function withSpecificData(
        string $id,
        string $firstname,
        string $lastname,
        string $username,
        string $email
    ): User {
        return User::create(
            new Uuid($id),
            new FirstnameValue($firstname),
            new LastnameValue($lastname),
            new UsernameValue($username),
            new EmailValue($email),
            new PasswordValue(password_hash('password123', PASSWORD_BCRYPT))
        );
    }
}

