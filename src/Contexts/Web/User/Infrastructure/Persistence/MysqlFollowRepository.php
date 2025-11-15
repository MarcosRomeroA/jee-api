<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Infrastructure\Persistence;

use App\Contexts\Web\User\Domain\Follow;
use App\Contexts\Web\User\Domain\FollowRepository;
use App\Contexts\Web\User\Domain\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class MysqlFollowRepository extends ServiceEntityRepository implements
    FollowRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Follow::class);
    }

    public function findByFollowerAndFollowed(
        User $follower,
        User $followed,
    ): ?Follow {
        return $this->findOneBy([
            "follower" => $follower,
            "followed" => $followed,
        ]);
    }

    public function findFollowersByUser(
        User $user,
        ?int $limit = null,
        ?int $offset = null,
    ): array {
        $qb = $this->createQueryBuilder("f")
            ->where("f.followed = :user")
            ->setParameter("user", $user);

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }
        if ($offset !== null) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    public function findFollowingsByUser(
        User $user,
        ?int $limit = null,
        ?int $offset = null,
    ): array {
        $qb = $this->createQueryBuilder("f")
            ->where("f.follower = :user")
            ->setParameter("user", $user);

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }
        if ($offset !== null) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    public function countFollowersByUser(User $user): int
    {
        return (int) $this->createQueryBuilder("f")
            ->select("COUNT(f.id)")
            ->where("f.followed = :user")
            ->setParameter("user", $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countFollowingsByUser(User $user): int
    {
        return (int) $this->createQueryBuilder("f")
            ->select("COUNT(f.id)")
            ->where("f.follower = :user")
            ->setParameter("user", $user)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
