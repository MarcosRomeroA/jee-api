<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Create;

use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\FileManager\FileManager;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\Post;
use App\Contexts\Web\Post\Domain\PostRepository;
use App\Contexts\Web\Post\Domain\ValueObject\BodyValue;
use App\Contexts\Web\User\Domain\User;

final readonly class PostCreator
{
    public function __construct(
        private PostRepository $repository,
        private FileManager $fileManager,
        private EventBus $bus,
    )
    {
    }

    public function __invoke(
        Uuid $id,
        BodyValue $body,
        string $imagePath,
        User $user
    ): void
    {
        $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
        $filename = uniqid().'.'.$extension;
        $this->fileManager->upload($imagePath, 'post/post', $filename);

        $post = Post::create($id, $body, $user, $filename);

        $this->repository->save($post);
        $this->bus->publish(...$user->pullDomainEvents());
    }
}