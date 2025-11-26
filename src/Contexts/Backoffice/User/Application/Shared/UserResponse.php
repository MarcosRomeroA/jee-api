<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\User\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\User\Domain\User;

final class UserResponse extends Response
{
    public function __construct(
        private readonly string $id,
        private readonly string $firstname,
        private readonly string $lastname,
        private readonly string $username,
        private readonly string $email,
        private readonly ?string $description,
        private readonly string $createdAt,
        private readonly ?string $verifiedAt,
        private readonly ?string $disabledAt,
    ) {
    }

    public static function fromEntity(User $user): self
    {
        return new self(
            id: $user->getId()->value(),
            firstname: $user->getFirstname()->value(),
            lastname: $user->getLastname()->value(),
            username: $user->getUsername()->value(),
            email: $user->getEmail()->value(),
            description: $user->getDescription(),
            createdAt: $user->getCreatedAt()->format('Y-m-d\TH:i:sP'),
            verifiedAt: $user->getVerifiedAt()?->format('Y-m-d\TH:i:sP'),
            disabledAt: $user->getDisabledAt()?->format('Y-m-d\TH:i:sP'),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'username' => $this->username,
            'email' => $this->email,
            'description' => $this->description,
            'createdAt' => $this->createdAt,
            'verifiedAt' => $this->verifiedAt,
            'disabledAt' => $this->disabledAt,
            'isVerified' => $this->verifiedAt !== null,
            'isDisabled' => $this->disabledAt !== null,
        ];
    }
}
