<?php

declare(strict_types=1);

namespace App\Contexts\Web\Game\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\GameAccountRequirement;
use App\Contexts\Web\Game\Domain\GameAccountRequirementRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameAccountRequirement>
 */
final class MysqlGameAccountRequirementRepository extends ServiceEntityRepository implements GameAccountRequirementRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameAccountRequirement::class);
    }

    public function save(GameAccountRequirement $requirement): void
    {
        $this->getEntityManager()->persist($requirement);
        $this->getEntityManager()->flush();
    }

    public function findByGameId(Uuid $gameId): ?GameAccountRequirement
    {
        return $this->createQueryBuilder('r')
            ->innerJoin('r.game', 'g')
            ->where('g.id = :gameId')
            ->setParameter('gameId', $gameId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
