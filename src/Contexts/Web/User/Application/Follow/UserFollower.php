<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Follow;

use App\Contexts\Web\User\Domain\Follow;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Shared\Domain\CQRS\Event\EventBus;

final readonly class UserFollower
{
    public function __construct(
        private UserRepository $userRepository,
        private EventBus $bus,
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

        $follow = Follow::create($follower, $followed);

        $follower->addFollow($follow);

        $this->userRepository->save($follower);

        $this->bus->publish(...$follow->pullDomainEvents());
    }
}