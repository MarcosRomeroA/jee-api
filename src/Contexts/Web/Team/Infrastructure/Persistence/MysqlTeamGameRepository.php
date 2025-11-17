<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Game;
use App\Contexts\Web\Team\Domain\Exception\TeamGameNotFoundException;
use App\Contexts\Web\Team\Domain\Team;
use App\Contexts\Web\Team\Domain\TeamGame;
use App\Contexts\Web\Team\Domain\TeamGameRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TeamGame>
 *
 * @method TeamGame|null find($id, $lockMode = null, $lockVersion = null)
 * @method TeamGame|null findOneBy(array $criteria, array $orderBy = null)
 * @method TeamGame[]    findAll()
 * @method TeamGame[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class MysqlTeamGameRepository extends ServiceEntityRepository implements TeamGameRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TeamGame::class);
    }

    public function save(TeamGame $teamGame): void
    {
        $this->getEntityManager()->persist($teamGame);
        $this->getEntityManager()->flush();
    }

    public function findById(Uuid $id): ?TeamGame
    {
        return $this->find($id->value());
    }

    public function findByTeamAndGame(Team $team, Game $game): ?TeamGame
    {
        $teamGame = $this->createQueryBuilder('tg')
            ->where('tg.team = :team')
            ->andWhere('tg.game = :game')
            ->setParameter('team', $team)
            ->setParameter('game', $game)
            ->getQuery()
            ->getOneOrNullResult();


        if ($teamGame === null) {
            throw new TeamGameNotFoundException();
        }

        return $teamGame;
    }

    public function findByTeam(Uuid $teamId): array
    {
        return $this->createQueryBuilder('tg')
            ->where('tg.team = :teamId')
            ->setParameter('teamId', $teamId->value())
            ->getQuery()
            ->getResult();
    }

    public function findByGame(Uuid $gameId): array
    {
        return $this->createQueryBuilder('tg')
            ->where('tg.game = :gameId')
            ->setParameter('gameId', $gameId->value())
            ->getQuery()
            ->getResult();
    }

    public function delete(TeamGame $teamGame): void
    {
        $this->getEntityManager()->remove($teamGame);
        $this->getEntityManager()->flush();
    }

    public function existsByTeamAndGame(Uuid $teamId, Uuid $gameId): bool
    {
        $count = $this->createQueryBuilder('tg')
            ->select('COUNT(tg.id)')
            ->where('tg.team = :teamId')
            ->andWhere('tg.game = :gameId')
            ->setParameter('teamId', $teamId->value())
            ->setParameter('gameId', $gameId->value())
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $count > 0;
    }

    public function remove(TeamGame $teamGame): void
    {
        $this->getEntityManager()->remove($teamGame);
        $this->getEntityManager()->flush();
    }
}
