<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\Exception\EmailAlreadyExistsException;
use App\Contexts\Web\User\Domain\Exception\UsernameAlreadyExistsException;
use App\Contexts\Web\User\Domain\Exception\UserNotFoundException;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Domain\ValueObject\EmailValue;
use App\Contexts\Web\User\Domain\ValueObject\UsernameValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class MysqlUserRepository extends ServiceEntityRepository implements
    UserRepository
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
        $user = $this->findOneBy(["email.value" => $email]);

        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    public function findById(Uuid $id): User
    {
        $user = $this->find($id);

        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    public function checkIfUsernameExists(UsernameValue $username): void
    {
        $user = $this->findOneBy(["username.value" => $username->value()]);

        if ($user) {
            throw new UsernameAlreadyExistsException();
        }
    }

    public function checkIfEmailExists(EmailValue $email): void
    {
        $user = $this->findOneBy(["email.value" => $email->value()]);

        if ($user) {
            throw new EmailAlreadyExistsException();
        }
    }

    /**
     * @param array $criteria
     * @return array<User>
     */
    public function searchByCriteria(array $criteria): array
    {
        $qb = $this->createQueryBuilder("u");

        if (isset($criteria["username"]) && $criteria["username"] !== "") {
            $qb->andWhere("u.username.value LIKE :username")->setParameter(
                "username",
                "%" . $criteria["username"] . "%",
            );
        }

        $limit = $criteria["limit"] ?? null;
        $offset = $criteria["offset"] ?? null;

        if ($limit !== null) {
            $qb->setMaxResults((int) $limit);
        }
        if ($offset !== null) {
            $qb->setFirstResult((int) $offset);
        }

        return $qb->getQuery()->getResult();
    }

    public function findByUsername(UsernameValue $username): User
    {
        $user = $this->findOneBy(["username.value" => $username->value()]);

        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }
    public function countByCriteria(array $criteria): int
    {
        $qb = $this->createQueryBuilder("u")->select("COUNT(u.id)");

        if (isset($criteria["username"]) && $criteria["username"] !== "") {
            $qb->andWhere("u.username.value LIKE :username")->setParameter(
                "username",
                "%" . $criteria["username"] . "%",
            );
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function delete(User $user): void
    {
        $this->getEntityManager()->remove($user);
        $this->getEntityManager()->flush();
    }
}
