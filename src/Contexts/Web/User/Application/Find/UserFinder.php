<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Find;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Application\Shared\UserResponse;
use App\Contexts\Web\User\Domain\UserRepository;

final readonly class UserFinder
{
    public function __construct(
        private UserRepository $userRepository,
    )
    {
    }

    public function __invoke(Uuid $id): UserResponse
    {
        $user = $this->userRepository->findById($id);

        return UserResponse::fromEntity($user);
    }
}