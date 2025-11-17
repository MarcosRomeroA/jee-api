<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\SocialNetwork;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\User\Domain\UserSocialNetwork;
use App\Contexts\Web\User\Domain\UserSocialNetworkRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserSocialNetwork>
 *
 * @method UserSocialNetwork|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserSocialNetwork|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserSocialNetwork[]    findAll()
 * @method UserSocialNetwork[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class MysqlUserSocialNetworkRepository extends ServiceEntityRepository implements UserSocialNetworkRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserSocialNetwork::class);
    }

    public function save(UserSocialNetwork $userSocialNetwork): void
    {
        $this->getEntityManager()->persist($userSocialNetwork);
        $this->getEntityManager()->flush();
    }

    public function findById(Uuid $id): ?UserSocialNetwork
    {
        return $this->find($id->value());
    }

    public function findByUserAndSocialNetwork(User $user, SocialNetwork $socialNetwork): ?UserSocialNetwork
    {
        return $this->createQueryBuilder('usn')
            ->where('usn.user = :user')
            ->andWhere('usn.socialNetwork = :socialNetwork')
            ->andWhere('usn.deletedAt IS NULL')
            ->setParameter('user', $user)
            ->setParameter('socialNetwork', $socialNetwork)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByUserAndSocialNetworkIncludingDeleted(User $user, SocialNetwork $socialNetwork): ?UserSocialNetwork
    {
        return $this->createQueryBuilder('usn')
            ->where('usn.user = :user')
            ->andWhere('usn.socialNetwork = :socialNetwork')
            ->setParameter('user', $user)
            ->setParameter('socialNetwork', $socialNetwork)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('usn')
            ->where('usn.user = :user')
            ->andWhere('usn.deletedAt IS NULL')
            ->setParameter('user', $user)
            ->orderBy('usn.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function delete(UserSocialNetwork $userSocialNetwork): void
    {
        $this->getEntityManager()->remove($userSocialNetwork);
        $this->getEntityManager()->flush();
    }
}
