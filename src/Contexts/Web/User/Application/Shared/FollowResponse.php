<?php declare(strict_types=1);

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
        public readonly string $profileImage,
    )
    {
    }

    public static function fromEntity(Follow $follow): self
    {
        return new self(
            $follow->getFollowed()->getId()->value(),
            $follow->getFollowed()->getUsername()->value(),
            $follow->getFollowed()->getFirstname()->value(),
            $follow->getFollowed()->getLastname()->value(),
            $follow->getFollowed()->getProfileImage()->value()
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}