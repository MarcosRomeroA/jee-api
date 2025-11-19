<?php

declare(strict_types=1);

namespace App\Contexts\Web\Player\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Player\Domain\Exception\PlayerNotFoundException;
use App\Contexts\Web\Player\Domain\Player;
use App\Contexts\Web\Player\Domain\PlayerRepository;
use App\Contexts\Web\Player\Domain\ValueObject\UsernameValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Player>
 *
 * @method Player|null find($id, $lockMode = null, $lockVersion = null)
 * @method Player|null findOneBy(array $criteria, array $orderBy = null)
 * @method Player[]    findAll()
 * @method Player[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class MysqlPlayerRepository extends ServiceEntityRepository implements PlayerRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Player::class);
    }

    public function save(Player $player): void
    {
        $this->getEntityManager()->persist($player);
        $this->getEntityManager()->flush();
    }

    public function findById(Uuid $id): Player
    {
        $player = $this->find($id);

        if ($player === null) {
            throw new PlayerNotFoundException($id->value());
        }

        return $player;
    }

    public function findByUserId(Uuid $userId): array
    {
        return $this->createQueryBuilder("p")
            ->andWhere("p.user = :userId")
            ->setParameter("userId", $userId)
            ->getQuery()
            ->getResult();
    }

    public function findByGameId(Uuid $gameId): array
    {
        return $this->createQueryBuilder("p")
            ->join("p.gameRoles", "gr")
            ->andWhere("gr.game = :gameId")
            ->setParameter("gameId", $gameId)
            ->getQuery()
            ->getResult();
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder("p")->getQuery()->getResult();
    }

    public function delete(Player $player): void
    {
        $this->getEntityManager()->remove($player);
        $this->getEntityManager()->flush();
    }

    public function existsById(Uuid $id): bool
    {
        return $this->count(["id" => $id]) > 0;
    }

    public function searchWithPagination(
        ?string $query,
        ?Uuid $gameId,
        ?Uuid $teamId,
        ?Uuid $userId,
        int $limit,
        int $offset,
    ): array {
        $qb = $this->createQueryBuilder("p");

        if ($query !== null && $query !== "") {
            $qb->andWhere("p.username.username LIKE :query")->setParameter(
                "query",
                "%{$query}%",
            );
        }

        if ($gameId !== null) {
            $qb->join("p.gameRoles", "gr")
                ->join("gr.game", "g")
                ->andWhere("g.id = :gameId")
                ->setParameter("gameId", $gameId);
        }

        if ($teamId !== null) {
            $qb->join("p.teamPlayers", "tp")
                ->join("tp.team", "t")
                ->andWhere("t.id = :teamId")
                ->setParameter("teamId", $teamId);
        }

        if ($userId !== null) {
            $qb->andWhere("p.user = :userId")->setParameter("userId", $userId);
        }

        return $qb
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy("p.createdAt", "DESC")
            ->getQuery()
            ->getResult();
    }

    public function countSearch(
        ?string $query,
        ?Uuid $gameId,
        ?Uuid $teamId,
        ?Uuid $userId,
    ): int {
        $qb = $this->createQueryBuilder("p")->select("COUNT(p.id)");

        if ($query !== null && $query !== "") {
            $qb->andWhere("p.username.username LIKE :query")->setParameter(
                "query",
                "%{$query}%",
            );
        }

        if ($gameId !== null) {
            $qb->join("p.gameRoles", "gr")
                ->join("gr.game", "g")
                ->andWhere("g.id = :gameId")
                ->setParameter("gameId", $gameId);
        }

        if ($teamId !== null) {
            $qb->join("p.teamPlayers", "tp")
                ->join("tp.team", "t")
                ->andWhere("t.id = :teamId")
                ->setParameter("teamId", $teamId);
        }

        if ($userId !== null) {
            $qb->andWhere("p.user = :userId")->setParameter("userId", $userId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function existsByUserIdAndUsernameAndGameId(
        Uuid $userId,
        UsernameValue $username,
        Uuid $gameId,
    ): bool {
        $count = (int) $this->createQueryBuilder("p")
            ->select("COUNT(p.id)")
            ->join("p.gameRoles", "gr")
            ->andWhere("p.user = :userId")
            ->andWhere("p.username.username = :username")
            ->andWhere("gr.game = :gameId")
            ->setParameter("userId", $userId)
            ->setParameter("username", $username->value())
            ->setParameter("gameId", $gameId)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }
}
