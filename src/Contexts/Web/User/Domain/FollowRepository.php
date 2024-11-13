<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Domain;

interface FollowRepository
{
    public function findByFollowerAndFollowed(User $follower, User $followed): ?Follow;
}