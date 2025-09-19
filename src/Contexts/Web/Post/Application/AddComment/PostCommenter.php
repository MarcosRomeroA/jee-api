<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\AddComment;

use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\Comment;
use App\Contexts\Web\Post\Domain\PostRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class PostCommenter
{
    public function __construct(
        private PostRepository $repository,
        private EventBus $bus,
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function __invoke(
        Uuid $postId,
        Comment $comment,
    ): void
    {
        $post = $this->repository->findById($postId);
        $post->addComment($comment);
        $this->entityManager->flush();
        $this->bus->publish(...$post->pullDomainEvents());
    }
}