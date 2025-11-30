<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\Exception\TeamNotFoundException;
use App\Contexts\Web\Team\Domain\Team;
use App\Contexts\Web\Team\Domain\TeamRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Team>
 *
 * @method Team|null find($id, $lockMode = null, $lockVersion = null)
 * @method Team|null findOneBy(array $criteria, array $orderBy = null)
 * @method Team[]    findAll()
 * @method Team[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class MysqlTeamRepository extends ServiceEntityRepository implements TeamRepository
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

    public function findById(Uuid $id): Team
    {
        $team = $this->findOneBy(["id" => $id->value()]);

        if (!$team) {
            throw new TeamNotFoundException($id->value());
        }

        return $team;
    }

    public function findByCreatorId(Uuid $creatorId): array
    {
        return $this->createQueryBuilder("t")
            ->join("t.teamUsers", "tu")
            ->join("tu.user", "u")
            ->andWhere("tu.isCreator = true")
            ->andWhere("u.id = :creatorId")
            ->setParameter("creatorId", $creatorId)
            ->getQuery()
            ->getResult();
    }

    public function search(string $query): array
    {
        return $this->createQueryBuilder("t")
            ->andWhere("t.name.value LIKE :query")
            ->setParameter("query", "%" . $query . "%")
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
        return $this->count(["id" => $id]) > 0;
    }

    public function searchWithPagination(
        ?string $name,
        ?Uuid $gameId,
        ?Uuid $creatorId,
        ?Uuid $userId,
        ?Uuid $tournamentId,
        ?Uuid $ownerOrLeaderId,
        ?Uuid $myCreatorId,
        ?Uuid $myLeaderId,
        int $limit,
        int $offset,
    ): array {
        $qb = $this->createQueryBuilder("t");

        if ($name !== null) {
            $qb->andWhere("t.name.value LIKE :name")->setParameter(
                "name",
                "%" . $name . "%",
            );
        }

        if ($gameId !== null) {
            $qb->join("t.teamGames", "tg")
                ->join("tg.game", "g")
                ->andWhere("g.id = :gameId")
                ->setParameter("gameId", $gameId);
        }

        if ($creatorId !== null) {
            $qb->join("t.teamUsers", "tuCreator")
                ->join("tuCreator.user", "creatorUser")
                ->andWhere("tuCreator.isCreator = true")
                ->andWhere("creatorUser.id = :creatorId")
                ->setParameter("creatorId", $creatorId);
        }

        if ($userId !== null) {
            $qb->join("t.teamUsers", "tu")
                ->join("tu.user", "tuUser")
                ->andWhere("tuUser.id = :userId")
                ->setParameter("userId", $userId);
        }

        if ($tournamentId !== null) {
            $qb->join("App\Contexts\Web\Tournament\Domain\TournamentTeam", "tt", "WITH", "tt.team = t.id")
                ->andWhere("tt.tournament = :tournamentId")
                ->setParameter("tournamentId", $tournamentId);
        }

        if ($ownerOrLeaderId !== null) {
            $qb->join("t.teamUsers", "tuOwnerLeader")
                ->join("tuOwnerLeader.user", "ownerLeaderUser")
                ->andWhere("(tuOwnerLeader.isCreator = true OR tuOwnerLeader.isLeader = true)")
                ->andWhere("ownerLeaderUser.id = :ownerOrLeaderId")
                ->setParameter("ownerOrLeaderId", $ownerOrLeaderId);
        }

        if ($myCreatorId !== null) {
            $qb->join("t.teamUsers", "tuMyCreator")
                ->join("tuMyCreator.user", "myCreatorUser")
                ->andWhere("tuMyCreator.isCreator = true")
                ->andWhere("myCreatorUser.id = :myCreatorId")
                ->setParameter("myCreatorId", $myCreatorId);
        }

        if ($myLeaderId !== null) {
            $qb->join("t.teamUsers", "tuMyLeader")
                ->join("tuMyLeader.user", "myLeaderUser")
                ->andWhere("tuMyLeader.isLeader = true")
                ->andWhere("myLeaderUser.id = :myLeaderId")
                ->setParameter("myLeaderId", $myLeaderId);
        }

        return $qb
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    public function countSearch(
        ?string $name,
        ?Uuid $gameId,
        ?Uuid $creatorId,
        ?Uuid $userId,
        ?Uuid $tournamentId,
        ?Uuid $ownerOrLeaderId,
        ?Uuid $myCreatorId,
        ?Uuid $myLeaderId,
    ): int {
        $qb = $this->createQueryBuilder("t")->select("COUNT(DISTINCT t.id)");

        if ($name !== null) {
            $qb->andWhere("t.name.value LIKE :name")->setParameter(
                "name",
                "%" . $name . "%",
            );
        }

        if ($gameId !== null) {
            $qb->join("t.teamGames", "tg")
                ->join("tg.game", "g")
                ->andWhere("g.id = :gameId")
                ->setParameter("gameId", $gameId);
        }

        if ($creatorId !== null) {
            $qb->join("t.teamUsers", "tuCreator")
                ->join("tuCreator.user", "creatorUser")
                ->andWhere("tuCreator.isCreator = true")
                ->andWhere("creatorUser.id = :creatorId")
                ->setParameter("creatorId", $creatorId);
        }

        if ($userId !== null) {
            $qb->join("t.teamUsers", "tu")
                ->join("tu.user", "tuUser")
                ->andWhere("tuUser.id = :userId")
                ->setParameter("userId", $userId);
        }

        if ($tournamentId !== null) {
            $qb->join("App\Contexts\Web\Tournament\Domain\TournamentTeam", "tt", "WITH", "tt.team = t.id")
                ->andWhere("tt.tournament = :tournamentId")
                ->setParameter("tournamentId", $tournamentId);
        }

        if ($ownerOrLeaderId !== null) {
            $qb->join("t.teamUsers", "tuOwnerLeader")
                ->join("tuOwnerLeader.user", "ownerLeaderUser")
                ->andWhere("(tuOwnerLeader.isCreator = true OR tuOwnerLeader.isLeader = true)")
                ->andWhere("ownerLeaderUser.id = :ownerOrLeaderId")
                ->setParameter("ownerOrLeaderId", $ownerOrLeaderId);
        }

        if ($myCreatorId !== null) {
            $qb->join("t.teamUsers", "tuMyCreator")
                ->join("tuMyCreator.user", "myCreatorUser")
                ->andWhere("tuMyCreator.isCreator = true")
                ->andWhere("myCreatorUser.id = :myCreatorId")
                ->setParameter("myCreatorId", $myCreatorId);
        }

        if ($myLeaderId !== null) {
            $qb->join("t.teamUsers", "tuMyLeader")
                ->join("tuMyLeader.user", "myLeaderUser")
                ->andWhere("tuMyLeader.isLeader = true")
                ->andWhere("myLeaderUser.id = :myLeaderId")
                ->setParameter("myLeaderId", $myLeaderId);
        }

        try {
            $result = $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException $e) {
            $result = 0;
        }

        return (int) $result;
    }

    public function searchByCriteria(array $criteria): array
    {
        $qb = $this->createQueryBuilder("t")
            ->leftJoin("t.teamUsers", "tu", "WITH", "tu.isCreator = true")
            ->leftJoin("tu.user", "creator");

        if (isset($criteria['teamId']) && $criteria['teamId'] !== '') {
            $qb->andWhere("t.id = :teamId")
                ->setParameter("teamId", $criteria['teamId']);
        }

        if (isset($criteria['name']) && $criteria['name'] !== '') {
            $qb->andWhere("t.name.value LIKE :name")
                ->setParameter("name", "%" . $criteria['name'] . "%");
        }

        if (isset($criteria['creatorUsername']) && $criteria['creatorUsername'] !== '') {
            $qb->andWhere("creator.username.value LIKE :creatorUsername")
                ->setParameter("creatorUsername", "%" . $criteria['creatorUsername'] . "%");
        }

        if (isset($criteria['creatorEmail']) && $criteria['creatorEmail'] !== '') {
            $qb->andWhere("creator.email.value LIKE :creatorEmail")
                ->setParameter("creatorEmail", "%" . $criteria['creatorEmail'] . "%");
        }

        if (isset($criteria['disabled'])) {
            $qb->andWhere("t.isDisabled = :disabled")
                ->setParameter("disabled", $criteria['disabled']);
        }

        $limit = $criteria['limit'] ?? 20;
        $offset = $criteria['offset'] ?? 0;

        return $qb
            ->orderBy("t.createdAt.value", "DESC")
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    public function countByCriteria(array $criteria): int
    {
        $qb = $this->createQueryBuilder("t")
            ->select("COUNT(DISTINCT t.id)")
            ->leftJoin("t.teamUsers", "tu", "WITH", "tu.isCreator = true")
            ->leftJoin("tu.user", "creator");

        if (isset($criteria['teamId']) && $criteria['teamId'] !== '') {
            $qb->andWhere("t.id = :teamId")
                ->setParameter("teamId", $criteria['teamId']);
        }

        if (isset($criteria['name']) && $criteria['name'] !== '') {
            $qb->andWhere("t.name.value LIKE :name")
                ->setParameter("name", "%" . $criteria['name'] . "%");
        }

        if (isset($criteria['creatorUsername']) && $criteria['creatorUsername'] !== '') {
            $qb->andWhere("creator.username.value LIKE :creatorUsername")
                ->setParameter("creatorUsername", "%" . $criteria['creatorUsername'] . "%");
        }

        if (isset($criteria['creatorEmail']) && $criteria['creatorEmail'] !== '') {
            $qb->andWhere("creator.email.value LIKE :creatorEmail")
                ->setParameter("creatorEmail", "%" . $criteria['creatorEmail'] . "%");
        }

        if (isset($criteria['disabled'])) {
            $qb->andWhere("t.isDisabled = :disabled")
                ->setParameter("disabled", $criteria['disabled']);
        }

        try {
            $result = $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException $e) {
            $result = 0;
        }

        return (int) $result;
    }
}
