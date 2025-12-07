<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\User\Domain\User;

final class UserResponse extends Response
{
    public function __construct(
        public readonly string $id,
        public readonly string $firstname,
        public readonly string $lastname,
        public readonly string $username,
        public readonly string $email,
        public readonly ?string $profileImage,
        public readonly ?string $description,
        public readonly string $createdAt,
    ) {
    }

    /**
     * Creates a UserResponse for search/list contexts (uses 128px thumbnail).
     */
    public static function fromEntity(User $user, string $cdnBaseUrl): self
    {
        return new self(
            $user->getId()->value(),
            $user->getFirstname()->value(),
            $user->getLastname()->value(),
            $user->getUsername()->value(),
            $user->getEmail()->value(),
            $user->getAvatarUrl(128, $cdnBaseUrl),
            $user->getDescription(),
            $user->getCreatedAt()->format('Y-m-d\TH:i:s\Z'),
        );
    }

    /**
     * Creates a UserResponse for profile/detail contexts (uses full 512px image).
     */
    public static function fromEntityFull(User $user, string $cdnBaseUrl): self
    {
        return new self(
            $user->getId()->value(),
            $user->getFirstname()->value(),
            $user->getLastname()->value(),
            $user->getUsername()->value(),
            $user->getEmail()->value(),
            $user->getAvatarUrl(512, $cdnBaseUrl),
            $user->getDescription(),
            $user->getCreatedAt()->format('Y-m-d\TH:i:s\Z'),
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
