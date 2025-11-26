<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Admin\Infrastructure\Persistence;

use App\Contexts\Backoffice\Admin\Domain\Admin;
use App\Contexts\Backoffice\Admin\Domain\AdminRepository;
use App\Contexts\Backoffice\Admin\Domain\ValueObject\AdminUserValue;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Admin>
 *
 * @method Admin|null find($id, $lockMode = null, $lockVersion = null)
 * @method Admin|null findOneBy(array $criteria, array $orderBy = null)
 * @method Admin[]    findAll()
 * @method Admin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class MysqlAdminRepository extends ServiceEntityRepository implements AdminRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Admin::class);
    }

    public function save(Admin $admin): void
    {
        $this->getEntityManager()->persist($admin);
        $this->getEntityManager()->flush();
    }

    public function findById(Uuid $id): ?Admin
    {
        return $this->find($id);
    }

    public function findByUser(AdminUserValue $user): ?Admin
    {
        return $this->findOneBy(['user.value' => $user->value()]);
    }

    public function existsByUser(AdminUserValue $user): bool
    {
        return $this->findByUser($user) !== null;
    }

    public function searchByCriteria(array $criteria): array
    {
        $qb = $this->createQueryBuilder('a');

        // Filter out deleted admins by default
        if (!isset($criteria['includeDeleted']) || !$criteria['includeDeleted']) {
            $qb->andWhere('a.deletedAt IS NULL');
        }

        $this->applyCriteriaFilters($qb, $criteria);

        $limit = $criteria['limit'] ?? null;
        $offset = $criteria['offset'] ?? null;

        if ($limit !== null) {
            $qb->setMaxResults((int) $limit);
        }
        if ($offset !== null) {
            $qb->setFirstResult((int) $offset);
        }

        return $qb->getQuery()->getResult();
    }

    public function countByCriteria(array $criteria): int
    {
        $qb = $this->createQueryBuilder('a')->select('COUNT(a.id)');

        // Filter out deleted admins by default
        if (!isset($criteria['includeDeleted']) || !$criteria['includeDeleted']) {
            $qb->andWhere('a.deletedAt IS NULL');
        }

        $this->applyCriteriaFilters($qb, $criteria);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    private function applyCriteriaFilters(\Doctrine\ORM\QueryBuilder $qb, array $criteria): void
    {
        if (isset($criteria['name']) && $criteria['name'] !== '') {
            $qb->andWhere('a.name.value LIKE :name')
                ->setParameter('name', '%' . $criteria['name'] . '%');
        }

        if (isset($criteria['user']) && $criteria['user'] !== '') {
            $qb->andWhere('a.user.value LIKE :user')
                ->setParameter('user', '%' . $criteria['user'] . '%');
        }
    }
}
