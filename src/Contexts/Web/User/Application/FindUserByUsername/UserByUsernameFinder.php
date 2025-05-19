<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\FindUserByUsername;

use App\Contexts\Shared\Domain\FileManager\FileManager;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Application\Shared\UserResponse;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Domain\ValueObject\UsernameValue;

final readonly class UserByUsernameFinder
{
    public function __construct(
        private UserRepository $userRepository,
        private FileManager $fileManager,
    )
    {
    }

    public function __invoke(UsernameValue $username): UserResponse
    {
        $user = $this->userRepository->findByUsername($username);

        return UserResponse::fromEntity($user, $this->fileManager->generateTemporaryUrl('user/profile', $user->getProfileImage()->value()));
    }
}