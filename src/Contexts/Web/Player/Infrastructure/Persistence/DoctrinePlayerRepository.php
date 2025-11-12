<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Player\Domain\Player;
use App\Contexts\Web\Player\Domain\PlayerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrinePlayerRepository extends ServiceEntityRepository implements PlayerRepository
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

    public function findById(Uuid $id): ?Player
    {
        return $this->find($id->value());
    }

    public function findByUserId(Uuid $userId): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.user = :userId')
            ->setParameter('userId', $userId->value())
            ->getQuery()
            ->getResult();
    }

    public function findByGameId(Uuid $gameId): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.gameRole', 'gr')
            ->andWhere('gr.game = :gameId')
            ->setParameter('gameId', $gameId->value())
            ->getQuery()
            ->getResult();
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('p')
            ->getQuery()
            ->getResult();
    }

    public function delete(Player $player): void
    {
        $this->getEntityManager()->remove($player);
        $this->getEntityManager()->flush();
    }

    public function existsById(Uuid $id): bool
    {
        return $this->count(['id' => $id->value()]) > 0;
    }

    public function searchWithPagination(
        ?string $query,
        ?Uuid $gameId,
        int $limit,
        int $offset
    ): array {
        $qb = $this->createQueryBuilder('p');

        if ($query !== null && $query !== '') {
            $qb->andWhere('p.username LIKE :query')
               ->setParameter('query', "%{$query}%");
        }

        if ($gameId !== null) {
            $qb->join('p.gameRole', 'gr')
               ->join('gr.game', 'g')
               ->andWhere('g.id = :gameId')
               ->setParameter('gameId', $gameId->value());
        }

        return $qb->setMaxResults($limit)
                  ->setFirstResult($offset)
                  ->orderBy('p.createdAt', 'DESC')
                  ->getQuery()
                  ->getResult();
    }

    public function countSearch(?string $query, ?Uuid $gameId): int
    {
        $qb = $this->createQueryBuilder('p')
                   ->select('COUNT(p.id)');

        if ($query !== null && $query !== '') {
            $qb->andWhere('p.username LIKE :query')
               ->setParameter('query', "%{$query}%");
        }

        if ($gameId !== null) {
            $qb->join('p.gameRole', 'gr')
               ->join('gr.game', 'g')
               ->andWhere('g.id = :gameId')
               ->setParameter('gameId', $gameId->value());
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}

