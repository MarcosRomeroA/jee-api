<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Dislike;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\PostRepository;
use App\Contexts\Web\User\Domain\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class PostDisliker
{
    public function __construct(
        private PostRepository $postRepository,
        private UserRepository $userRepository,
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

        $user = $this->userRepository->findById($userId);

        $post->removeLike($user);

        $this->entityManager->flush();
    }
}