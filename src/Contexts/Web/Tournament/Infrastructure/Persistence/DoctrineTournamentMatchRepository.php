<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Domain\TournamentMatch;
use App\Contexts\Web\Tournament\Domain\TournamentMatchRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineTournamentMatchRepository extends ServiceEntityRepository implements TournamentMatchRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TournamentMatch::class);
    }

    public function save(TournamentMatch $match): void
    {
        $this->getEntityManager()->persist($match);
        $this->getEntityManager()->flush();
    }

    public function findById(Uuid $id): ?TournamentMatch
    {
        return $this->find($id->value());
    }

    public function findByTournamentId(Uuid $tournamentId): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.tournament = :tournamentId')
            ->setParameter('tournamentId', $tournamentId->value())
            ->orderBy('m.round', 'ASC')
            ->addOrderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByTournamentIdAndRound(Uuid $tournamentId, int $round): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.tournament = :tournamentId')
            ->andWhere('m.round = :round')
            ->setParameter('tournamentId', $tournamentId->value())
            ->setParameter('round', $round)
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function delete(TournamentMatch $match): void
    {
        $this->getEntityManager()->remove($match);
        $this->getEntityManager()->flush();
    }
}

