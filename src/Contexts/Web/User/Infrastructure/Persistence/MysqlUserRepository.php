<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\Exception\UserNotFoundException;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\User\Domain\UserRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MysqlUserRepository extends ServiceEntityRepository implements UserRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $user): void
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function searchAll(): array
    {
        return $this->findAll();
    }

    public function findByEmail(string $email): User
    {
        $user = $this->findOneBy(['email.value' => $email]);

        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    public function findById(Uuid $id): User
    {
        $user = $this->findOneBy(['id' => $id]);

        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }
}