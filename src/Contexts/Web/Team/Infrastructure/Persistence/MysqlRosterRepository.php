<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\Exception\RosterNotFoundException;
use App\Contexts\Web\Team\Domain\Roster;
use App\Contexts\Web\Team\Domain\RosterRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Roster>
 */
final class MysqlRosterRepository extends ServiceEntityRepository implements RosterRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Roster::class);
    }

    public function save(Roster $roster): void
    {
        $this->getEntityManager()->persist($roster);
        $this->getEntityManager()->flush();
    }

    public function findById(Uuid $id): Roster
    {
        $roster = $this->findOneBy(['id' => $id->value()]);

        if (!$roster) {
            throw new RosterNotFoundException($id->value());
        }

        return $roster;
    }

    public function findByTeamId(Uuid $teamId): array
    {
        return $this->createQueryBuilder('r')
            ->join('r.team', 't')
            ->andWhere('t.id = :teamId')
            ->setParameter('teamId', $teamId)
            ->orderBy('r.createdAt.value', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByTeamIdWithPagination(Uuid $teamId, int $limit, int $offset): array
    {
        return $this->createQueryBuilder('r')
            ->join('r.team', 't')
            ->andWhere('t.id = :teamId')
            ->setParameter('teamId', $teamId)
            ->orderBy('r.createdAt.value', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    public function countByTeamId(Uuid $teamId): int
    {
        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->join('r.team', 't')
            ->andWhere('t.id = :teamId')
            ->setParameter('teamId', $teamId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function delete(Roster $roster): void
    {
        $this->getEntityManager()->remove($roster);
        $this->getEntityManager()->flush();
    }

    public function existsById(Uuid $id): bool
    {
        return $this->count(['id' => $id]) > 0;
    }
}
