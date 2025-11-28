<?php

declare(strict_types=1);

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
        return $this->findOneBy(["id" => $id->value()]);
    }

    public function findByTournamentAndTeam(
        Uuid $tournamentId,
        Uuid $teamId,
    ): ?TournamentTeam {
        return $this->createQueryBuilder("tt")
            ->where("tt.tournament = :tournamentId")
            ->andWhere("tt.team = :teamId")
            ->setParameter("tournamentId", $tournamentId)
            ->setParameter("teamId", $teamId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByTournament(Uuid $tournamentId): array
    {
        return $this->createQueryBuilder("tt")
            ->where("tt.tournament = :tournamentId")
            ->setParameter("tournamentId", $tournamentId)
            ->getQuery()
            ->getResult();
    }

    public function findByUserId(Uuid $userId): array
    {
        return $this->createQueryBuilder("tt")
            ->select("tt")
            ->join("tt.team", "t")
            ->join("t.teamPlayers", "tp")
            ->join("tp.player", "p")
            ->where("p.user = :userId")
            ->setParameter("userId", $userId)
            ->getQuery()
            ->getResult();
    }

    public function delete(TournamentTeam $tournamentTeam): void
    {
        $this->getEntityManager()->remove($tournamentTeam);
        $this->getEntityManager()->flush();
    }

    public function isUserRegisteredInTournament(Uuid $tournamentId, Uuid $userId): bool
    {
        // Check if user is a member of any team registered in the tournament
        // (creator and leader are now part of teamUsers with isCreator/isLeader flags)
        $result = $this->createQueryBuilder("tt")
            ->select("COUNT(tt.id)")
            ->join("tt.team", "t")
            ->join("t.teamUsers", "tu")
            ->join("tu.user", "u")
            ->where("tt.tournament = :tournamentId")
            ->andWhere("u.id = :userId")
            ->setParameter("tournamentId", $tournamentId)
            ->setParameter("userId", $userId)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $result > 0;
    }

    public function findUserRegisteredTournamentIds(array $tournamentIds, Uuid $userId): array
    {
        if (empty($tournamentIds)) {
            return [];
        }

        $tournamentIdValues = array_map(fn (Uuid $id) => $id->value(), $tournamentIds);

        // Check if user is a member of any team registered in the tournaments
        // (creator and leader are now part of teamUsers with isCreator/isLeader flags)
        $results = $this->createQueryBuilder("tt")
            ->select("IDENTITY(tt.tournament) as tournamentId")
            ->join("tt.team", "t")
            ->join("t.teamUsers", "tu")
            ->join("tu.user", "u")
            ->where("tt.tournament IN (:tournamentIds)")
            ->andWhere("u.id = :userId")
            ->setParameter("tournamentIds", $tournamentIdValues)
            ->setParameter("userId", $userId)
            ->groupBy("tt.tournament")
            ->getQuery()
            ->getResult();

        return array_column($results, 'tournamentId');
    }
}
