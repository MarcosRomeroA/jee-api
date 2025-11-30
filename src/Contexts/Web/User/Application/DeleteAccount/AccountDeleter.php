<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\DeleteAccount;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\PostRepository;
use App\Contexts\Web\User\Domain\UserRepository;

final readonly class AccountDeleter
{
    public function __construct(
        private UserRepository $userRepository,
        private PostRepository $postRepository,
    ) {
    }

    public function __invoke(Uuid $userId): void
    {
        $user = $this->userRepository->findById($userId);

        // Nullify sharedPostId for posts that shared user's posts
        $this->postRepository->nullifySharedPostIdByUserId($userId);

        // Delete all user's posts (real delete)
        $this->postRepository->deleteByUserId($userId);

        // Delete user (real delete)
        $this->userRepository->delete($user);
    }
}
