<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\FindUserByUsername;

use App\Contexts\Web\User\Application\Shared\UserResponse;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Domain\ValueObject\UsernameValue;

final readonly class UserByUsernameFinder
{
    public function __construct(
        private UserRepository $userRepository,
        private string $cdnBaseUrl,
    )
    {
    }

    public function __invoke(UsernameValue $username): UserResponse
    {
        $user = $this->userRepository->findByUsername($username);

        return UserResponse::fromEntityFull($user, $this->cdnBaseUrl);
    }
}
