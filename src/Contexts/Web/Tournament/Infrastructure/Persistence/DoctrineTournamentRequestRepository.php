<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Domain\TournamentRequest;
use App\Contexts\Web\Tournament\Domain\TournamentRequestRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineTournamentRequestRepository extends ServiceEntityRepository implements TournamentRequestRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TournamentRequest::class);
    }

    public function save(TournamentRequest $request): void
    {
        $this->getEntityManager()->persist($request);
        $this->getEntityManager()->flush();
    }

    public function findById(Uuid $id): ?TournamentRequest
    {
        return $this->findOneBy(['id' => $id->value()]);
    }

    public function findPendingByTournamentAndTeam(
        Uuid $tournamentId,
        Uuid $teamId,
    ): ?TournamentRequest {
        return $this->createQueryBuilder('tr')
            ->join('tr.tournament', 't')
            ->join('tr.team', 'team')
            ->where('t.id = :tournamentId')
            ->andWhere('team.id = :teamId')
            ->andWhere('tr.status = :status')
            ->setParameter('tournamentId', $tournamentId->value())
            ->setParameter('teamId', $teamId->value())
            ->setParameter('status', 'pending')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return TournamentRequest[]
     */
    public function findPendingByTournament(Uuid $tournamentId): array
    {
        return $this->createQueryBuilder('tr')
            ->join('tr.tournament', 't')
            ->where('t.id = :tournamentId')
            ->andWhere('tr.status = :status')
            ->setParameter('tournamentId', $tournamentId->value())
            ->setParameter('status', 'pending')
            ->orderBy('tr.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return TournamentRequest[]
     */
    public function findAllPending(): array
    {
        return $this->createQueryBuilder('tr')
            ->where('tr.status = :status')
            ->setParameter('status', 'pending')
            ->orderBy('tr.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
