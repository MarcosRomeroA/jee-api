<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class ShareResponse extends Response
{
    public function __construct(
        public readonly string $userId,
        public readonly string $username,
        public readonly string $firstname,
        public readonly string $lastname,
        public readonly ?string $profileImage,
        public readonly string $sharedAt,
    ) {
    }

    public function toArray(): array
    {
        return [
            'userId' => $this->userId,
            'username' => $this->username,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'profileImage' => $this->profileImage,
            'sharedAt' => $this->sharedAt,
        ];
    }
}
