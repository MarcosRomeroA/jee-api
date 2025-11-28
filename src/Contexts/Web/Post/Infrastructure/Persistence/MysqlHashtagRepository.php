<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\Hashtag;
use App\Contexts\Web\Post\Domain\HashtagRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class MysqlHashtagRepository implements HashtagRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function save(Hashtag $hashtag): void
    {
        $this->entityManager->persist($hashtag);
        $this->entityManager->flush();
    }

    public function findByTag(string $tag): ?Hashtag
    {
        $normalizedTag = Hashtag::normalize($tag);

        return $this->entityManager
            ->getRepository(Hashtag::class)
            ->findOneBy(['tag' => $normalizedTag]);
    }

    public function findById(Uuid $id): ?Hashtag
    {
        return $this->entityManager
            ->getRepository(Hashtag::class)
            ->find($id);
    }

    public function getPopularHashtags(int $days = 30, int $limit = 10): array
    {
        $date = new \DateTimeImmutable();
        $date = $date->modify("-{$days} days");

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('h.tag')
            ->from(Hashtag::class, 'h')
            ->where('h.updatedAt.value >= :date')
            ->andWhere('h.deletedAt.value IS NULL')
            ->orderBy('h.count', 'DESC')
            ->setParameter('date', $date)
            ->setMaxResults($limit);

        $result = $qb->getQuery()->getResult();

        // Extract just the tag strings from the result
        return array_map(fn ($row) => $row['tag'], $result);
    }

    public function searchByCriteria(array $criteria): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('h')
            ->from(Hashtag::class, 'h');

        $this->applyCriteria($qb, $criteria);

        $qb->orderBy('h.count', 'DESC');

        $limit = $criteria['limit'] ?? 20;
        $offset = $criteria['offset'] ?? 0;

        $qb->setMaxResults($limit)
            ->setFirstResult($offset);

        return $qb->getQuery()->getResult();
    }

    public function countByCriteria(array $criteria): int
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('COUNT(h.id)')
            ->from(Hashtag::class, 'h');

        $this->applyCriteria($qb, $criteria);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    private function applyCriteria(\Doctrine\ORM\QueryBuilder $qb, array $criteria): void
    {
        $includeDeleted = $criteria['includeDeleted'] ?? false;
        if (!$includeDeleted) {
            $qb->andWhere('h.deletedAt.value IS NULL');
        }

        if (isset($criteria['disabled']) && $criteria['disabled'] === true) {
            $qb->andWhere('h.deletedAt.value IS NOT NULL');
        } elseif (isset($criteria['disabled']) && $criteria['disabled'] === false) {
            $qb->andWhere('h.deletedAt.value IS NULL');
        }

        if (!empty($criteria['q'])) {
            $qb->andWhere('h.tag LIKE :q')
                ->setParameter('q', '%' . $criteria['q'] . '%');
        }

        if (!empty($criteria['tag'])) {
            $normalizedTag = Hashtag::normalize($criteria['tag']);
            $qb->andWhere('h.tag = :tag')
                ->setParameter('tag', $normalizedTag);
        }
    }
}
