<?php

declare(strict_types=1);

namespace App\Contexts\Web\Event\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Event\Domain\Event;
use App\Contexts\Web\Event\Domain\EventRepository;
use App\Contexts\Web\Event\Domain\EventType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class MysqlEventRepository extends ServiceEntityRepository implements EventRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function save(Event $event): void
    {
        $this->getEntityManager()->persist($event);
        $this->getEntityManager()->flush();
    }

    public function findById(Uuid $id): ?Event
    {
        return $this->findOneBy(['id' => $id->value()]);
    }

    public function delete(Event $event): void
    {
        $this->getEntityManager()->remove($event);
        $this->getEntityManager()->flush();
    }

    public function existsById(Uuid $id): bool
    {
        return $this->count(['id' => $id->value()]) > 0;
    }

    public function searchUpcoming(
        ?Uuid $gameId,
        ?EventType $type,
        int $limit,
        int $offset,
    ): array {
        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.startAt >= :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('e.startAt', 'ASC');

        if ($gameId !== null) {
            $qb->andWhere('e.game = :gameId')
                ->setParameter('gameId', $gameId->value());
        }

        if ($type !== null) {
            $qb->andWhere('e.type = :type')
                ->setParameter('type', $type->value);
        }

        return $qb
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    public function countUpcoming(
        ?Uuid $gameId,
        ?EventType $type,
    ): int {
        $qb = $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->andWhere('e.startAt >= :now')
            ->setParameter('now', new \DateTimeImmutable());

        if ($gameId !== null) {
            $qb->andWhere('e.game = :gameId')
                ->setParameter('gameId', $gameId->value());
        }

        if ($type !== null) {
            $qb->andWhere('e.type = :type')
                ->setParameter('type', $type->value);
        }

        try {
            $result = $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException) {
            $result = 0;
        }

        return (int) $result;
    }
}
