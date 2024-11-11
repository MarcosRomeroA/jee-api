<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Follow;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\Follow;
use App\Contexts\Web\User\Domain\UserRepository;

final readonly class UserFollower
{
    public function __construct(
        private UserRepository $userRepository
    )
    {
    }

    public function __invoke(
        Uuid $userId,
        Uuid $userToFollowId
    ): void
    {
        $follower = $this->userRepository->findById($userId);
        $followed = $this->userRepository->findById($userToFollowId);

        $follow = new Follow();
        $follow->setFollowed($followed);
        $follow->setFollower($follower);

        $follower->follow($follow);
        $this->userRepository->save($follower);
    }
}