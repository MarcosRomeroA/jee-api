<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Domain\TournamentStatus;
use App\Contexts\Web\Tournament\Domain\TournamentStatusRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineTournamentStatusRepository
    extends ServiceEntityRepository
    implements TournamentStatusRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TournamentStatus::class);
    }

    public function save(TournamentStatus $status): void
    {
        $this->getEntityManager()->persist($status);
        $this->getEntityManager()->flush();
    }

    public function findById(Uuid $id): ?TournamentStatus
    {
        return $this->findOneBy(["id" => $id->value()]);
    }

    public function findByName(string $name): ?TournamentStatus
    {
        return $this->createQueryBuilder("ts")
            ->where("ts.name = :name")
            ->setParameter("name", $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder("ts")
            ->orderBy("ts.name", "ASC")
            ->getQuery()
            ->getResult();
    }
}
