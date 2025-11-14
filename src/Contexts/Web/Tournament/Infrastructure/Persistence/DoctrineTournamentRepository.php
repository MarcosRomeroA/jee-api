<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Domain\Exception\TournamentNotFoundException;
use App\Contexts\Web\Tournament\Domain\Tournament;
use App\Contexts\Web\Tournament\Domain\TournamentRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineTournamentRepository extends ServiceEntityRepository implements TournamentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tournament::class);
    }

    public function save(Tournament $tournament): void
    {
        $this->getEntityManager()->persist($tournament);
        $this->getEntityManager()->flush();
    }

    public function findById(Uuid $id): Tournament
    {
        $tournament = $this->findOneBy(['id' => $id]);

        if (!$tournament){
            throw new TournamentNotFoundException($id->value());
        }

        return $tournament;
    }

    public function search(
        ?string $query,
        ?Uuid $gameId,
        ?Uuid $responsibleId,
        bool $open,
        int $limit,
        int $offset
    ): array {
        $qb = $this->createQueryBuilder('t')
            ->andWhere('t.deletedAt IS NULL');

        if ($query !== null) {
            $qb->andWhere('t.name LIKE :query')
               ->setParameter('query', '%' . $query . '%');
        }

        if ($gameId !== null) {
            $qb->join('t.game', 'g')
               ->andWhere('g.id = :gameId')
               ->setParameter('gameId', $gameId);
        }

        if ($responsibleId !== null) {
            $qb->join('t.responsible', 'r')
               ->andWhere('r.id = :responsibleId')
               ->setParameter('responsibleId', $responsibleId);
        }

        if ($open) {
            $qb->join('t.status', 's')
               ->andWhere('s.name = :statusName')
               ->setParameter('statusName', 'active')
               ->andWhere('t.registeredTeams < t.maxTeams');
        }

        return $qb->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    public function countSearch(
        ?string $query,
        ?Uuid $gameId,
        ?Uuid $responsibleId,
        bool $open
    ): int {
        $qb = $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.deletedAt IS NULL');

        if ($query !== null) {
            $qb->andWhere('t.name LIKE :query')
               ->setParameter('query', '%' . $query . '%');
        }

        if ($gameId !== null) {
            $qb->join('t.game', 'g')
               ->andWhere('g.id = :gameId')
               ->setParameter('gameId', $gameId);
        }

        if ($responsibleId !== null) {
            $qb->join('t.responsible', 'r')
               ->andWhere('r.id = :responsibleId')
               ->setParameter('responsibleId', $responsibleId);
        }

        if ($open) {
            $qb->join('t.status', 's')
               ->andWhere('s.name = :statusName')
               ->setParameter('statusName', 'active')
               ->andWhere('t.registeredTeams < t.maxTeams');
        }

        try {
            $result = $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException $e) {
            $result = 0;
        }

        return (int) $result;
    }

    public function delete(Tournament $tournament): void
    {
        $this->getEntityManager()->remove($tournament);
        $this->getEntityManager()->flush();
    }

    public function existsById(Uuid $id): bool
    {
        return $this->count(['id' => $id]) > 0;
    }
}

