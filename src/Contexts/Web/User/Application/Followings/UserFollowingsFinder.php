<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Followings;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Application\Shared\FollowCollectionResponse;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Domain\FollowRepository;

final readonly class UserFollowingsFinder
{
    public function __construct(
        private UserRepository $userRepository,
        private FollowRepository $followRepository,
        private string $cdnBaseUrl,
    ) {
    }

    public function __invoke(
        Uuid $id,
        ?int $limit = null,
        ?int $offset = null,
    ): FollowCollectionResponse {
        $user = $this->userRepository->findById($id);

        $limit = $limit ?? 10;
        $offset = $offset ?? 0;

        $followings = $this->followRepository->findFollowingsByUser($user, $limit, $offset);
        $total = $this->followRepository->countFollowingsByUser($user);

        return new FollowCollectionResponse(
            $followings,
            $this->cdnBaseUrl,
            false,
            $limit,
            $offset,
            $total,
        );
    }
}
