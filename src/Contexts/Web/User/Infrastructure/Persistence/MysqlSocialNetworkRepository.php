<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\SocialNetwork;
use App\Contexts\Web\User\Domain\SocialNetworkRepository;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\User\Domain\ValueObject\SocialNetworkCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SocialNetwork>
 *
 * @method SocialNetwork|null find($id, $lockMode = null, $lockVersion = null)
 * @method SocialNetwork|null findOneBy(array $criteria, array $orderBy = null)
 * @method SocialNetwork[]    findAll()
 * @method SocialNetwork[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class MysqlSocialNetworkRepository extends ServiceEntityRepository implements SocialNetworkRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SocialNetwork::class);
    }

    public function findById(Uuid $id): ?SocialNetwork
    {
        return $this->find($id->value());
    }

    public function findByCode(SocialNetworkCode $code): ?SocialNetwork
    {
        return $this->createQueryBuilder('sn')
            ->where('sn.code.code = :code')
            ->setParameter('code', $code->value())
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAvailableForUser(User $user): array
    {
        return $this->createQueryBuilder('sn')
            ->where('sn.id NOT IN (
                SELECT IDENTITY(usn.socialNetwork)
                FROM App\Contexts\Web\User\Domain\UserSocialNetwork usn
                WHERE usn.user = :user
                AND usn.deletedAt IS NULL
            )')
            ->setParameter('user', $user)
            ->orderBy('sn.name.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
