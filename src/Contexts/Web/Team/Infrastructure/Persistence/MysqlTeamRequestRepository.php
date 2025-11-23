<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\TeamRequest;
use App\Contexts\Web\Team\Domain\TeamRequestRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TeamRequest>
 *
 * @method TeamRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method TeamRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method TeamRequest[]    findAll()
 * @method TeamRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class MysqlTeamRequestRepository extends ServiceEntityRepository implements TeamRequestRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TeamRequest::class);
    }

    public function save(TeamRequest $request): void
    {
        $this->getEntityManager()->persist($request);
        $this->getEntityManager()->flush();
    }

    public function findById(Uuid $id): ?TeamRequest
    {
        return $this->findOneBy(["id" => $id->value()]);
    }

    /**
     * @deprecated Use findPendingByTeamAndUser instead
     */
    public function findPendingByTeamAndPlayer(
        Uuid $teamId,
        Uuid $playerId,
    ): ?TeamRequest {
        return $this->createQueryBuilder("tr")
            ->where("tr.team = :teamId")
            ->andWhere("tr.player = :playerId")
            ->andWhere("tr.status = :status")
            ->setParameter("teamId", $teamId)
            ->setParameter("playerId", $playerId)
            ->setParameter("status", "pending")
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findPendingByTeamAndUser(
        Uuid $teamId,
        Uuid $userId,
    ): ?TeamRequest {
        return $this->createQueryBuilder("tr")
            ->where("tr.team = :teamId")
            ->andWhere("tr.user = :userId")
            ->andWhere("tr.status = :status")
            ->setParameter("teamId", $teamId)
            ->setParameter("userId", $userId)
            ->setParameter("status", "pending")
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findPendingByTeam(Uuid $teamId): array
    {
        return $this->createQueryBuilder("tr")
            ->where("tr.team = :teamId")
            ->andWhere("tr.status = :status")
            ->setParameter("teamId", $teamId)
            ->setParameter("status", "pending")
            ->orderBy("tr.createdAt", "DESC")
            ->getQuery()
            ->getResult();
    }

    /**
     * @deprecated Use findPendingByUser instead
     */
    public function findPendingByPlayer(Uuid $playerId): array
    {
        return $this->createQueryBuilder("tr")
            ->where("tr.player = :playerId")
            ->andWhere("tr.status = :status")
            ->setParameter("playerId", $playerId)
            ->setParameter("status", "pending")
            ->orderBy("tr.createdAt", "DESC")
            ->getQuery()
            ->getResult();
    }

    public function findPendingByUser(Uuid $userId): array
    {
        return $this->createQueryBuilder("tr")
            ->where("tr.user = :userId")
            ->andWhere("tr.status = :status")
            ->setParameter("userId", $userId)
            ->setParameter("status", "pending")
            ->orderBy("tr.createdAt", "DESC")
            ->getQuery()
            ->getResult();
    }

    public function findAllPending(): array
    {
        return $this->createQueryBuilder("tr")
            ->where("tr.status = :status")
            ->setParameter("status", "pending")
            ->orderBy("tr.createdAt", "ASC")
            ->getQuery()
            ->getResult();
    }
}
