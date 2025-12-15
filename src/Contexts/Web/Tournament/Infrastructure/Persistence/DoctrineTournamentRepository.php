<?php

declare(strict_types=1);

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
        $tournament = $this->findOneBy(["id" => $id->value()]);

        if (!$tournament) {
            throw new TournamentNotFoundException($id->value());
        }

        return $tournament;
    }

    public function search(
        ?string $name,
        ?Uuid $gameId,
        ?Uuid $statusId,
        ?Uuid $responsibleId,
        bool $open,
        int $limit,
        int $offset,
        bool $upcoming = false,
        ?Uuid $excludeUserId = null,
    ): array {
        $qb = $this->createQueryBuilder("t")
            ->andWhere("t.deletedAt IS NULL")
            ->andWhere("t.isDisabled = false");

        if ($name !== null) {
            $qb->andWhere("t.name LIKE :name")->setParameter(
                "name",
                "%" . $name . "%",
            );
        }

        if ($gameId !== null) {
            $qb->join("t.game", "g")
                ->andWhere("g.id = :gameId")
                ->setParameter("gameId", $gameId);
        }

        if ($statusId !== null) {
            $qb->join("t.status", "s")
                ->andWhere("s.id = :statusId")
                ->setParameter("statusId", $statusId);
        }

        if ($responsibleId !== null) {
            $qb->join("t.responsible", "r")
                ->andWhere("r.id = :responsibleId")
                ->setParameter("responsibleId", $responsibleId);
        }

        if ($open) {
            if ($statusId === null) {
                $qb->join("t.status", "s");
            }
            $qb->andWhere("s.name = :statusName")
                ->setParameter("statusName", "Active")
                ->andWhere("t.registeredTeams < t.maxTeams");
        }

        if ($upcoming) {
            $now = new \DateTimeImmutable();
            $maxDate = $now->modify('+30 days');
            $qb->andWhere("t.startAt > :now")
                ->andWhere("t.startAt <= :maxDate")
                ->setParameter("now", $now)
                ->setParameter("maxDate", $maxDate);

            // Excluir torneos donde el usuario ya está inscrito
            if ($excludeUserId !== null) {
                $qb->leftJoin("t.tournamentTeams", "tt")
                    ->leftJoin("tt.team", "team")
                    ->leftJoin("team.teamUsers", "tu")
                    ->andWhere(
                        $qb->expr()->orX(
                            $qb->expr()->isNull("tu.user"),
                            $qb->expr()->neq("tu.user", ":excludeUserId")
                        )
                    )
                    ->setParameter("excludeUserId", $excludeUserId->value());
            }
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
        ?Uuid $statusId,
        ?Uuid $responsibleId,
        bool $open,
        bool $upcoming = false,
        ?Uuid $excludeUserId = null,
    ): int {
        $qb = $this->createQueryBuilder("t")
            ->select("COUNT(DISTINCT t.id)")
            ->andWhere("t.deletedAt IS NULL")
            ->andWhere("t.isDisabled = false");

        if ($name !== null) {
            $qb->andWhere("t.name LIKE :name")->setParameter(
                "name",
                "%" . $name . "%",
            );
        }

        if ($gameId !== null) {
            $qb->join("t.game", "g")
                ->andWhere("g.id = :gameId")
                ->setParameter("gameId", $gameId);
        }

        if ($statusId !== null) {
            $qb->join("t.status", "s")
                ->andWhere("s.id = :statusId")
                ->setParameter("statusId", $statusId);
        }

        if ($responsibleId !== null) {
            $qb->join("t.responsible", "r")
                ->andWhere("r.id = :responsibleId")
                ->setParameter("responsibleId", $responsibleId);
        }

        if ($open) {
            if ($statusId === null) {
                $qb->join("t.status", "s");
            }
            $qb->andWhere("s.name = :statusName")
                ->setParameter("statusName", "Active")
                ->andWhere("t.registeredTeams < t.maxTeams");
        }

        if ($upcoming) {
            $now = new \DateTimeImmutable();
            $maxDate = $now->modify('+30 days');
            $qb->andWhere("t.startAt > :now")
                ->andWhere("t.startAt <= :maxDate")
                ->setParameter("now", $now)
                ->setParameter("maxDate", $maxDate);

            // Excluir torneos donde el usuario ya está inscrito
            if ($excludeUserId !== null) {
                $qb->leftJoin("t.tournamentTeams", "tt")
                    ->leftJoin("tt.team", "team")
                    ->leftJoin("team.teamUsers", "tu")
                    ->andWhere(
                        $qb->expr()->orX(
                            $qb->expr()->isNull("tu.user"),
                            $qb->expr()->neq("tu.user", ":excludeUserId")
                        )
                    )
                    ->setParameter("excludeUserId", $excludeUserId->value());
            }
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
        return $this->count(["id" => $id]) > 0;
    }

    public function searchByCriteria(array $criteria): array
    {
        $qb = $this->createQueryBuilder("t")
            ->leftJoin("t.responsible", "r")
            ->andWhere("t.deletedAt IS NULL");

        if (!empty($criteria['tournamentId'])) {
            $qb->andWhere("t.id = :tournamentId")
                ->setParameter("tournamentId", $criteria['tournamentId']);
        }

        if (!empty($criteria['name'])) {
            $qb->andWhere("t.name LIKE :name")
                ->setParameter("name", "%" . $criteria['name'] . "%");
        }

        if (!empty($criteria['responsibleUsername'])) {
            $qb->andWhere("r.username.value LIKE :responsibleUsername")
                ->setParameter("responsibleUsername", "%" . $criteria['responsibleUsername'] . "%");
        }

        if (!empty($criteria['responsibleEmail'])) {
            $qb->andWhere("r.email.value LIKE :responsibleEmail")
                ->setParameter("responsibleEmail", "%" . $criteria['responsibleEmail'] . "%");
        }

        if (isset($criteria['disabled'])) {
            $qb->andWhere("t.isDisabled = :disabled")
                ->setParameter("disabled", $criteria['disabled']);
        }

        $limit = $criteria['limit'] ?? 20;
        $offset = $criteria['offset'] ?? 0;

        return $qb
            ->orderBy("t.createdAt", "DESC")
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    public function countByCriteria(array $criteria): int
    {
        $qb = $this->createQueryBuilder("t")
            ->select("COUNT(t.id)")
            ->leftJoin("t.responsible", "r")
            ->andWhere("t.deletedAt IS NULL");

        if (!empty($criteria['tournamentId'])) {
            $qb->andWhere("t.id = :tournamentId")
                ->setParameter("tournamentId", $criteria['tournamentId']);
        }

        if (!empty($criteria['name'])) {
            $qb->andWhere("t.name LIKE :name")
                ->setParameter("name", "%" . $criteria['name'] . "%");
        }

        if (!empty($criteria['responsibleUsername'])) {
            $qb->andWhere("r.username.value LIKE :responsibleUsername")
                ->setParameter("responsibleUsername", "%" . $criteria['responsibleUsername'] . "%");
        }

        if (!empty($criteria['responsibleEmail'])) {
            $qb->andWhere("r.email.value LIKE :responsibleEmail")
                ->setParameter("responsibleEmail", "%" . $criteria['responsibleEmail'] . "%");
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

    public function findWonByUserId(Uuid $userId, int $limit, int $offset): array
    {
        $qb = $this->createQueryBuilder("t")
            ->join("t.firstPlaceTeam", "team")
            ->join("team.teamUsers", "tu")
            ->join("tu.user", "u")
            ->andWhere("u.id = :userId")
            ->andWhere("t.deletedAt IS NULL")
            ->setParameter("userId", $userId->value())
            ->orderBy("t.updatedAt", "DESC")
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        return $qb->getQuery()->getResult();
    }

    public function countWonByUserId(Uuid $userId): int
    {
        $qb = $this->createQueryBuilder("t")
            ->select("COUNT(t.id)")
            ->join("t.firstPlaceTeam", "team")
            ->join("team.teamUsers", "tu")
            ->join("tu.user", "u")
            ->andWhere("u.id = :userId")
            ->andWhere("t.deletedAt IS NULL")
            ->setParameter("userId", $userId->value());

        try {
            $result = $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException $e) {
            $result = 0;
        }

        return (int) $result;
    }
}
