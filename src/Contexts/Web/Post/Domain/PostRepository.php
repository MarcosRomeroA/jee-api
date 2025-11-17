<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\User;

interface PostRepository
{
    public function save(Post $post): void;

    /**
     * @return array<Post>
     */
    public function searchAll(): array;

    /**
     * @return array<Post>|null
     */
    public function findByUser(User $user): ?array;

    public function findById(Uuid $id): Post;

    /**
     * @param array<Uuid> $ids
     * @return array<Post>
     */
    public function findByIds(array $ids): array;

    /**
     * @return array<Post>
     */
    public function searchFeed(Uuid $userId, ?array $criteria = null): array;

    public function checkIsPostExists(Uuid $id): void;

    public function searchByCriteria(array $criteria): array;

    public function countByCriteria(array $criteria): int;

    public function findSharesQuantity(Uuid $id): int;

    /**
     * @return array<Post>
     */
    public function findSharesByPostId(Uuid $postId, int $limit, int $offset): array;

    public function countSharesByPostId(Uuid $postId): int;

    public function countFeed(Uuid $userId): int;

    /**
     * @return array<Post>
     */
    public function findByHashtag(string $hashtag, int $limit, int $offset): array;

    public function countByHashtag(string $hashtag): int;
}
