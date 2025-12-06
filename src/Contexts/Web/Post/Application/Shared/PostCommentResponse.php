<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

class PostCommentResponse extends Response
{
    public function __construct(
        public readonly string $id,
        public readonly string $comment,
        public readonly string $userId,
        public readonly string $username,
        public readonly ?string $profileImage,
        public readonly string $createdAt,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'comment' => $this->comment,
            'userId' => $this->userId,
            'username' => $this->username,
            'profileImage' => $this->profileImage,
            'createdAt' => $this->createdAt,
        ];
    }
}
