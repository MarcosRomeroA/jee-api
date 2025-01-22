<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Delete;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\Exception\PostDeletionNotAllowedException;
use App\Contexts\Web\Post\Domain\PostRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class PostDeleter
{
    public function __construct(
        private PostRepository         $postRepository,
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function __invoke(
        Uuid $postId,
        Uuid $userId,
    ): void
    {

        $post = $this->postRepository->findById($postId);

        if ($post->getUser()->getId()->value() !== $userId->value()) {
            throw new PostDeletionNotAllowedException();
        }

        $this->entityManager->remove($post);

        $this->entityManager->flush();
    }
}