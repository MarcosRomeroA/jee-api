<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Like;

use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\Like;
use App\Contexts\Web\Post\Domain\PostRepository;
use App\Contexts\Web\User\Domain\UserRepository;

final readonly class PostLiker
{
    public function __construct(
        private PostRepository $postRepository,
        private UserRepository $userRepository,
        private EventBus $bus,
    ) {}

    public function __invoke(Uuid $postId, Uuid $userId): void
    {
        $post = $this->postRepository->findById($postId);

        $user = $this->userRepository->findById($userId);

        $likeId = Uuid::random();
        $like = Like::create($likeId, $user, $post);

        $post->addLike($like);

        $this->postRepository->save($post);

        $this->bus->publish(...$post->pullDomainEvents());
        $this->bus->publish(...$like->pullDomainEvents());
    }
}
