<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Domain\TournamentTeam;
use App\Contexts\Web\Tournament\Domain\TournamentTeamRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineTournamentTeamRepository extends ServiceEntityRepository implements TournamentTeamRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TournamentTeam::class);
    }

    public function save(TournamentTeam $tournamentTeam): void
    {
        $this->getEntityManager()->persist($tournamentTeam);
        $this->getEntityManager()->flush();
    }

    public function findById(Uuid $id): ?TournamentTeam
    {
        return $this->find($id->value());
    }

    public function findByTournamentAndTeam(Uuid $tournamentId, Uuid $teamId): ?TournamentTeam
    {
        return $this->createQueryBuilder('tt')
            ->where('tt.tournament = :tournamentId')
            ->andWhere('tt.team = :teamId')
            ->setParameter('tournamentId', $tournamentId->value())
            ->setParameter('teamId', $teamId->value())
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByTournament(Uuid $tournamentId): array
    {
        return $this->createQueryBuilder('tt')
            ->where('tt.tournament = :tournamentId')
            ->setParameter('tournamentId', $tournamentId->value())
            ->getQuery()
            ->getResult();
    }

    public function delete(TournamentTeam $tournamentTeam): void
    {
        $this->getEntityManager()->remove($tournamentTeam);
        $this->getEntityManager()->flush();
    }
}
