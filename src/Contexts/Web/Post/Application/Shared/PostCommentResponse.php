<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\Post\Domain\Comment;

class PostCommentResponse extends Response
{
    public function __construct(
        public readonly string $id,
        public readonly string $comment,
        public readonly string $user,
        public readonly string $createdAt
    )
    {
    }

    public static function fromEntity(Comment $comment): self
    {
        return new self(
            $comment->getId()->value(),
            $comment->getComment()->value(),
            $comment->getUser()->getUsername()->value(),
            $comment->getCreatedAt()->value()->format('Y-m-d H:i:s')
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}