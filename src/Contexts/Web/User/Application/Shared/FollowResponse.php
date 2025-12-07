<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\User\Domain\Follow;

class FollowResponse extends Response
{
    public function __construct(
        public readonly string $id,
        public readonly string $username,
        public readonly string $firstname,
        public readonly string $lastname,
        public readonly ?string $profileImage,
    ) {
    }

    public static function fromEntity(Follow $follow, string $cdnBaseUrl, bool $isFollower = false): self
    {
        // For followers list, return the follower (person who follows you)
        // For followings list, return the followed (person you follow)
        $user = $isFollower ? $follow->getFollower() : $follow->getFollowed();

        return new self(
            $user->getId()->value(),
            $user->getUsername()->value(),
            $user->getFirstname()->value(),
            $user->getLastname()->value(),
            $user->getAvatarUrl(128, $cdnBaseUrl),
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
