<?php
declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Unfollow;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\FollowRepository;
use App\Contexts\Web\User\Domain\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class UserUnfollower
{
    public function __construct(
        private UserRepository $userRepository,
        private FollowRepository $followRepository,
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function __invoke(
        Uuid $userId,
        Uuid $userToUnfollowId
    ): void
    {
        $follower = $this->userRepository->findById($userId);
        $unfollowed = $this->userRepository->findById($userToUnfollowId);

        $followedRelation = $this->followRepository->findByFollowerAndFollowed($follower, $unfollowed);

        if ($followedRelation !== null) {
            $this->entityManager->remove($followedRelation);
            $this->entityManager->flush();
        }
    }
}