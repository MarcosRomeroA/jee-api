<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Create;

use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\Post;
use App\Contexts\Web\Post\Domain\PostRepository;
use App\Contexts\Web\Post\Domain\ValueObject\BodyValue;
use App\Contexts\Web\User\Domain\User;

final readonly class PostCreator
{
    public function __construct(
        private PostRepository $repository,
        private EventBus $bus,
    )
    {
    }

    public function __invoke(
        Uuid $id,
        BodyValue $body,
        User $user
    ): void
    {
        $user = Post::create($id, $body, $user);
        $this->repository->save($user);
        $this->bus->publish(...$user->pullDomainEvents());
    }
}