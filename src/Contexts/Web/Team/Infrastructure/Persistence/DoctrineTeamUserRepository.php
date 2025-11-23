<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\TeamUser;
use App\Contexts\Web\Team\Domain\TeamUserRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineTeamUserRepository extends ServiceEntityRepository implements TeamUserRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TeamUser::class);
    }

    public function save(TeamUser $teamUser): void
    {
        $this->getEntityManager()->persist($teamUser);
        $this->getEntityManager()->flush();
    }

    public function findById(Uuid $id): ?TeamUser
    {
        return $this->findOneBy(["id" => $id->value()]);
    }

    public function findByTeamAndUser(
        Uuid $teamId,
        Uuid $userId,
    ): ?TeamUser {
        return $this->createQueryBuilder("tu")
            ->where("tu.team = :teamId")
            ->andWhere("tu.user = :userId")
            ->setParameter("teamId", $teamId)
            ->setParameter("userId", $userId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByTeam(Uuid $teamId): array
    {
        return $this->createQueryBuilder("tu")
            ->where("tu.team = :teamId")
            ->setParameter("teamId", $teamId)
            ->getQuery()
            ->getResult();
    }

    public function findByUserId(Uuid $userId): array
    {
        return $this->createQueryBuilder("tu")
            ->where("tu.user = :userId")
            ->setParameter("userId", $userId)
            ->getQuery()
            ->getResult();
    }

    public function delete(TeamUser $teamUser): void
    {
        $this->getEntityManager()->remove($teamUser);
        $this->getEntityManager()->flush();
    }
}
