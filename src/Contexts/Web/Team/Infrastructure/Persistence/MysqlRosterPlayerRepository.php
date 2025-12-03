<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\Exception\RosterPlayerNotFoundException;
use App\Contexts\Web\Team\Domain\RosterPlayer;
use App\Contexts\Web\Team\Domain\RosterPlayerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RosterPlayer>
 */
final class MysqlRosterPlayerRepository extends ServiceEntityRepository implements RosterPlayerRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RosterPlayer::class);
    }

    public function save(RosterPlayer $rosterPlayer): void
    {
        $this->getEntityManager()->persist($rosterPlayer);
        $this->getEntityManager()->flush();
    }

    public function findById(Uuid $id): RosterPlayer
    {
        $rosterPlayer = $this->findOneBy(['id' => $id->value()]);

        if (!$rosterPlayer) {
            throw new RosterPlayerNotFoundException($id->value());
        }

        return $rosterPlayer;
    }

    public function findByRosterId(Uuid $rosterId): array
    {
        return $this->createQueryBuilder('rp')
            ->join('rp.roster', 'r')
            ->andWhere('r.id = :rosterId')
            ->setParameter('rosterId', $rosterId)
            ->orderBy('rp.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByRosterAndPlayer(Uuid $rosterId, Uuid $playerId): ?RosterPlayer
    {
        return $this->createQueryBuilder('rp')
            ->join('rp.roster', 'r')
            ->join('rp.player', 'p')
            ->andWhere('r.id = :rosterId')
            ->andWhere('p.id = :playerId')
            ->setParameter('rosterId', $rosterId)
            ->setParameter('playerId', $playerId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function delete(RosterPlayer $rosterPlayer): void
    {
        $this->getEntityManager()->remove($rosterPlayer);
        $this->getEntityManager()->flush();
    }

    public function countStartersByRosterId(Uuid $rosterId): int
    {
        try {
            $result = $this->createQueryBuilder('rp')
                ->select('COUNT(rp.id)')
                ->join('rp.roster', 'r')
                ->andWhere('r.id = :rosterId')
                ->andWhere('rp.isStarter = true')
                ->setParameter('rosterId', $rosterId)
                ->getQuery()
                ->getSingleScalarResult();

            return (int) $result;
        } catch (NoResultException) {
            return 0;
        }
    }

    public function existsLeaderInRoster(Uuid $rosterId): bool
    {
        try {
            $result = $this->createQueryBuilder('rp')
                ->select('COUNT(rp.id)')
                ->join('rp.roster', 'r')
                ->andWhere('r.id = :rosterId')
                ->andWhere('rp.isLeader = true')
                ->setParameter('rosterId', $rosterId)
                ->getQuery()
                ->getSingleScalarResult();

            return (int) $result > 0;
        } catch (NoResultException) {
            return false;
        }
    }

    public function existsById(Uuid $id): bool
    {
        return $this->count(['id' => $id]) > 0;
    }
}
