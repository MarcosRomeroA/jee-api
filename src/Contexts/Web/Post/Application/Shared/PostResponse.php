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
        public readonly ?int $likesQuantity,
        public readonly ?int $sharesQuantity,
        public readonly ?int $commentsQuantity,
        public readonly bool $hasLiked = false,
        public readonly bool $hasShared = false,
    ) {
    }

    public static function fromEntity(Post $post, bool $includeSharedPost = false, ?string $currentUserId = null): self
    {
        $sharedPostResponse = null;
        if ($post->getSharedPost() && $includeSharedPost) {
            $sharedPostResponse = self::fromEntity($post->getSharedPost(), false, $currentUserId);
        }

        $hasLiked = false;
        $hasShared = false;

        if ($currentUserId !== null) {
            foreach ($post->getLikes()->toArray() as $like) {
                if ($like->getUser()->getId()->value() === $currentUserId) {
                    $hasLiked = true;
                    break;
                }
            }

            $hasShared = $post->hasBeenSharedByUser();
        }

        return new self(
            $post->getId()->value(),
            $post->getBody()->value(),
            $post->getUser()->getUsername()->value(),
            $post->getResourceUrls(),
            $post->getCreatedAt()->value()->format('Y-m-d H:i:s'),
            $post->getUser()->getUrlProfileImage(),
            $sharedPostResponse?->toArray(),
            count($post->getLikes()->toArray()),
            $post->getSharesQuantity(),
            count(array_filter($post->getComments()->toArray(), fn($c) => !$c->isDisabled())),
            $hasLiked,
            $hasShared,
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
