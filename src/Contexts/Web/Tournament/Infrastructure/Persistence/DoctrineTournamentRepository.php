<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Domain\Tournament;
use App\Contexts\Web\Tournament\Domain\TournamentRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function findById(Uuid $id): ?Tournament
    {
        return $this->find($id->value());
    }

    public function findByResponsibleId(Uuid $responsibleId): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.responsible = :responsibleId')
            ->setParameter('responsibleId', $responsibleId->value())
            ->andWhere('t.deletedAt IS NULL')
            ->getQuery()
            ->getResult();
    }

    public function findOpenTournaments(string $query = ''): array
    {
        $qb = $this->createQueryBuilder('t')
            ->andWhere('t.deletedAt IS NULL')
            ->andWhere('t.status.id = :active')
            ->setParameter('active', 'active')
            ->andWhere('t.registeredTeams < t.maxTeams');

        if ($query !== '') {
            $qb->andWhere('t.name LIKE :query')
                ->setParameter('query', '%' . $query . '%');
        }

        return $qb->getQuery()->getResult();
    }

    public function delete(Tournament $tournament): void
    {
        $this->getEntityManager()->remove($tournament);
        $this->getEntityManager()->flush();
    }

    public function existsById(Uuid $id): bool
    {
        return $this->count(['id' => $id->value()]) > 0;
    }
}

