<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\Post\Domain\Post;

final class PostResponse extends Response
{
    public function __construct(
        public readonly string $id,
        public readonly string $body,
        public readonly string $imageUrl,
        public readonly string $createdAt,
    )
    {
    }

    public static function fromEntity(Post $post): self
    {
        return new self(
            $post->getId()->value(),
            $post->getBody()->value(),
            $post->getImageUrl(),
            $post->getCreatedAt()->value()->format('Y-m-d H:i:s')
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}