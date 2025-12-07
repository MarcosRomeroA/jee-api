<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\User\Domain\UserPreference;
use App\Contexts\Web\User\Domain\UserPreferenceRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserPreference>
 */
final class MysqlUserPreferenceRepository extends ServiceEntityRepository implements UserPreferenceRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserPreference::class);
    }

    public function save(UserPreference $preference): void
    {
        $this->getEntityManager()->persist($preference);
        $this->getEntityManager()->flush();
    }

    public function findByUserId(Uuid $userId): ?UserPreference
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.user', 'u')
            ->where('u.id = :userId')
            ->setParameter('userId', $userId->value())
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByUser(User $user): ?UserPreference
    {
        return $this->findOneBy(['user' => $user]);
    }
}
