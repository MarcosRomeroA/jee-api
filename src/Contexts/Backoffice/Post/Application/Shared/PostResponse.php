<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Post\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\Post\Domain\Post;

final class PostResponse extends Response
{
    public function __construct(
        private readonly string $id,
        private readonly string $body,
        private readonly string $userId,
        private readonly string $username,
        private readonly string $userEmail,
        private readonly ?string $sharedPostId,
        private readonly string $createdAt,
        private readonly bool $disabled,
        private readonly ?string $moderationReason,
        private readonly ?string $disabledAt,
        private readonly int $likesCount,
        private readonly int $commentsCount,
    ) {
    }

    public static function fromEntity(Post $post): self
    {
        return new self(
            id: $post->getId()->value(),
            body: $post->getBody()->value(),
            userId: $post->getUser()->getId()->value(),
            username: $post->getUser()->getUsername()->value(),
            userEmail: $post->getUser()->getEmail()->value(),
            sharedPostId: $post->getSharedPostId()?->value(),
            createdAt: $post->getCreatedAt()->value()->format('Y-m-d\TH:i:sP'),
            disabled: $post->isDisabled(),
            moderationReason: $post->getModerationReason()?->value,
            disabledAt: $post->getDisabledAt()?->format('Y-m-d\TH:i:sP'),
            likesCount: $post->getLikes()?->count() ?? 0,
            commentsCount: $post->getComments()?->count() ?? 0,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'body' => $this->body,
            'userId' => $this->userId,
            'username' => $this->username,
            'userEmail' => $this->userEmail,
            'sharedPostId' => $this->sharedPostId,
            'createdAt' => $this->createdAt,
            'disabled' => $this->disabled,
            'moderationReason' => $this->moderationReason,
            'disabledAt' => $this->disabledAt,
            'likesCount' => $this->likesCount,
            'commentsCount' => $this->commentsCount,
        ];
    }
}
