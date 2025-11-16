<?php declare(strict_types=1);

namespace App\Contexts\Web\Game\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Exception\GameNotFoundException;
use App\Contexts\Web\Game\Domain\Game;
use App\Contexts\Web\Game\Domain\GameRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class MysqlGameRepository extends ServiceEntityRepository implements
    GameRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function save(Game $game): void
    {
        $this->getEntityManager()->persist($game);
        $this->getEntityManager()->flush();
    }

    public function findById(Uuid $id): Game
    {
        $game = $this->find($id);

        if (!$game) {
            throw new GameNotFoundException($id->value());
        }

        return $game;
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder("g")->getQuery()->getResult();
    }

    public function search(string $query): array
    {
        return $this->createQueryBuilder("g")
            ->andWhere("g.name LIKE :query")
            ->setParameter("query", "%" . $query . "%")
            ->getQuery()
            ->getResult();
    }

    public function existsById(Uuid $id): bool
    {
        return $this->count(["id" => $id]) > 0;
    }
}
