<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\Post\Domain\Post;

final class PostResponse extends Response
{
    public function __construct(
        public readonly string $id,
        public readonly string $body,
        public readonly string $username,
        public readonly array $resources,
        public readonly string $createdAt,
        public readonly ?string $urlProfileImage,
        public readonly ?array $sharedPost,
    )
    {
    }

    public static function fromEntity(Post $post, bool $hasShared = false): self
    {
        $sharedPostResponse = null;
        if ($post->getSharedPost() && $hasShared){
            $sharedPostResponse = self::fromEntity($post->getSharedPost());
        }

        return new self(
            $post->getId()->value(),
            $post->getBody()->value(),
            $post->getUser()->getUsername()->value(),
            $post->getResourceUrls(),
            $post->getCreatedAt()->value()->format('Y-m-d H:i:s'),
            $post->getUser()->getUrlProfileImage(),
            $sharedPostResponse?->toArray()
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}