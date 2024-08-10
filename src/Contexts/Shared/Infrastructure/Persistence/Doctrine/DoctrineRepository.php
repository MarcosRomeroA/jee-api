<?php declare(strict_types=1);

namespace App\Contexts\Shared\Infrastructure\Persistence\Doctrine;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineRepository extends ServiceEntityRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ManagerRegistry $managerRegistry
    )
    {
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    protected function persist(AggregateRoot $entity) : AggregateRoot
    {
        $this->entityManager->persist($entity);
        return $entity;
    }

    protected function persistAndFlush(AggregateRoot $entity) : AggregateRoot
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        return $entity;
    }

    protected function updateAll() : void
    {
        $this->entityManager->flush();
    }

    protected function remove(AggregateRoot $entity) : void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    public function repository(string $entityClass): EntityRepository
    {
        parent::__construct($this->managerRegistry, $entityClass);
        return $this;
    }
}