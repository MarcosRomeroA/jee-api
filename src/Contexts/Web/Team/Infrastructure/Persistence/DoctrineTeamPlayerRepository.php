<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\TeamPlayer;
use App\Contexts\Web\Team\Domain\TeamPlayerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineTeamPlayerRepository extends ServiceEntityRepository implements TeamPlayerRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TeamPlayer::class);
    }

    public function save(TeamPlayer $teamPlayer): void
    {
        $this->getEntityManager()->persist($teamPlayer);
        $this->getEntityManager()->flush();
    }

    public function findById(Uuid $id): ?TeamPlayer
    {
        return $this->find($id->value());
    }

    public function findByTeamAndPlayer(Uuid $teamId, Uuid $playerId): ?TeamPlayer
    {
        return $this->createQueryBuilder('tp')
            ->where('tp.team = :teamId')
            ->andWhere('tp.player = :playerId')
            ->setParameter('teamId', $teamId->value())
            ->setParameter('playerId', $playerId->value())
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByTeam(Uuid $teamId): array
    {
        return $this->createQueryBuilder('tp')
            ->where('tp.team = :teamId')
            ->setParameter('teamId', $teamId->value())
            ->getQuery()
            ->getResult();
    }

    public function delete(TeamPlayer $teamPlayer): void
    {
        $this->getEntityManager()->remove($teamPlayer);
        $this->getEntityManager()->flush();
    }
}
