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
        public readonly string $profileImage,
        public readonly ?string $description,
        public readonly string $createdAt,
    ) {
    }

    public static function fromEntity(User $user, string $profileImage): self
    {
        return new self(
            $user->getId()->value(),
            $user->getFirstname()->value(),
            $user->getLastname()->value(),
            $user->getUsername()->value(),
            $user->getEmail()->value(),
            $profileImage,
            $user->getDescription(),
            $user->getCreatedAt()->format('Y-m-d\TH:i:s\Z'),
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
