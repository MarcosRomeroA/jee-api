<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class LikeResponse extends Response
{
    public function __construct(
        public readonly string $userId,
        public readonly string $username,
        public readonly string $firstname,
        public readonly string $lastname,
        public readonly ?string $profileImage,
        public readonly string $likedAt,
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
            'likedAt' => $this->likedAt,
        ];
    }
}
