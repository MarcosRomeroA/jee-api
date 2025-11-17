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
}
