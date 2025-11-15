<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Domain;

interface FollowRepository
{
    public function findByFollowerAndFollowed(
        User $follower,
        User $followed,
    ): ?Follow;

    public function findFollowersByUser(
        User $user,
        ?int $limit = null,
        ?int $offset = null,
    ): array;

    public function findFollowingsByUser(
        User $user,
        ?int $limit = null,
        ?int $offset = null,
    ): array;

    public function countFollowersByUser(User $user): int;

    public function countFollowingsByUser(User $user): int;
}
