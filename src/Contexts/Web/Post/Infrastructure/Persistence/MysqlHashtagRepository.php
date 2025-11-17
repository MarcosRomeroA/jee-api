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
}
