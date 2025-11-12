<?php declare(strict_types=1);

namespace App\Contexts\Web\Game\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\GameRank;
use App\Contexts\Web\Game\Domain\GameRankRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineGameRankRepository extends ServiceEntityRepository implements GameRankRepository
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

    public function findById(Uuid $id): ?GameRank
    {
        return $this->find($id->value());
    }

    public function findByGameAndName(Uuid $gameId, string $name): ?GameRank
    {
        return $this->createQueryBuilder('gr')
            ->where('gr.game = :gameId')
            ->andWhere('gr.name = :name')
            ->setParameter('gameId', $gameId->value())
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByGame(Uuid $gameId): array
    {
        return $this->createQueryBuilder('gr')
            ->where('gr.game = :gameId')
            ->setParameter('gameId', $gameId->value())
            ->orderBy('gr.level', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

