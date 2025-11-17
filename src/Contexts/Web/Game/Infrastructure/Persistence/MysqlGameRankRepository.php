<?php

declare(strict_types=1);

namespace App\Contexts\Web\Game\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\GameRank;
use App\Contexts\Web\Game\Domain\GameRankRepository;
use App\Contexts\Web\Game\Domain\Exception\GameRankNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameRank>
 *
 * @method GameRank|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameRank|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameRank[]    findAll()
 * @method GameRank[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class MysqlGameRankRepository extends ServiceEntityRepository implements GameRankRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameRank::class);
    }

    public function save(GameRank $gameRank): void
    {
        $this->getEntityManager()->persist($gameRank);
        $this->getEntityManager()->flush();
    }

    public function findById(Uuid $id): GameRank
    {
        $gameRank = $this->findOneBy(["id" => $id]);

        if (!$gameRank) {
            throw new GameRankNotFoundException($id->value());
        }

        return $gameRank;
    }

    public function findByGame(Uuid $gameId): array
    {
        return $this->createQueryBuilder("gr")
            ->join("gr.game", "g")
            ->andWhere("g.id = :gameId")
            ->setParameter("gameId", $gameId->value())
            ->orderBy("gr.level", "ASC")
            ->getQuery()
            ->getResult();
    }

    public function existsById(Uuid $id): bool
    {
        return $this->count(["id" => $id]) > 0;
    }
}
