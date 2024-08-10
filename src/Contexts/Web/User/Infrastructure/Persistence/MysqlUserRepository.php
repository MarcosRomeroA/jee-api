<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Infrastructure\Persistence\Doctrine\DoctrineRepository;
use App\Contexts\Web\User\Domain\Exception\UserNotFoundException;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\User\Domain\UserRepository;

class MysqlUserRepository extends DoctrineRepository implements UserRepository
{
    public function save(User $user): void
    {
        $this->persistAndFlush($user);
    }

    public function searchAll(): array
    {
        return $this->repository(User::class)->findAll();
    }

    public function findByEmail(string $email): User
    {
        $user = $this->repository(User::class)->findOneBy(['email.value' => $email]);

        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    public function findById(Uuid $id): User
    {
        $user = $this->repository(User::class)->findOneBy(['id' => $id]);

        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }
}