<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\Team;
use App\Contexts\Web\Team\Domain\TeamRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineTeamRepository extends ServiceEntityRepository implements TeamRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }

    public function save(Team $team): void
    {
        $this->getEntityManager()->persist($team);
        $this->getEntityManager()->flush();
    }

    public function findById(Uuid $id): ?Team
    {
        return $this->find($id->value());
    }

    public function findByOwnerId(Uuid $ownerId): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.owner = :ownerId')
            ->setParameter('ownerId', $ownerId->value())
            ->getQuery()
            ->getResult();
    }

    public function search(string $query): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.name LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();
    }

    public function delete(Team $team): void
    {
        $this->getEntityManager()->remove($team);
        $this->getEntityManager()->flush();
    }

    public function existsById(Uuid $id): bool
    {
        return $this->count(['id' => $id->value()]) > 0;
    }

    public function searchWithPagination(
        ?string $query,
        ?Uuid $gameId,
        int $limit,
        int $offset
    ): array {
        $qb = $this->createQueryBuilder('t');

        if ($query !== null) {
            $qb->andWhere('t.name LIKE :query')
               ->setParameter('query', '%' . $query . '%');
        }

        if ($gameId !== null) {
            $qb->andWhere('t.gameId = :gameId')
               ->setParameter('gameId', $gameId->value());
        }

        return $qb->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    public function countSearch(?string $query, ?Uuid $gameId): int
    {
        $qb = $this->createQueryBuilder('t')
            ->select('COUNT(t.id)');

        if ($query !== null) {
            $qb->andWhere('t.name LIKE :query')
               ->setParameter('query', '%' . $query . '%');
        }

        if ($gameId !== null) {
            $qb->andWhere('t.gameId = :gameId')
               ->setParameter('gameId', $gameId->value());
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function searchMyTeamsWithPagination(
        Uuid $userId,
        ?string $query,
        ?Uuid $gameId,
        int $limit,
        int $offset
    ): array {
        $qb = $this->createQueryBuilder('t')
            ->andWhere('t.ownerId = :userId')
            ->setParameter('userId', $userId->value());

        if ($query !== null) {
            $qb->andWhere('t.name LIKE :query')
               ->setParameter('query', '%' . $query . '%');
        }

        if ($gameId !== null) {
            $qb->andWhere('t.gameId = :gameId')
               ->setParameter('gameId', $gameId->value());
        }

        return $qb->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    public function countMyTeams(Uuid $userId, ?string $query, ?Uuid $gameId): int
    {
        $qb = $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.ownerId = :userId')
            ->setParameter('userId', $userId->value());

        if ($query !== null) {
            $qb->andWhere('t.name LIKE :query')
               ->setParameter('query', '%' . $query . '%');
        }

        if ($gameId !== null) {
            $qb->andWhere('t.gameId = :gameId')
               ->setParameter('gameId', $gameId->value());
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}

