<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Followers;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Application\Shared\FollowCollectionResponse;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Domain\FollowRepository;

final readonly class UserFollowersFinder
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

        $follows = $this->followRepository->findFollowersByUser($user, $limit, $offset);
        $total = $this->followRepository->countFollowersByUser($user);

        return new FollowCollectionResponse(
            $follows,
            $this->cdnBaseUrl,
            true,
            $limit,
            $offset,
            $total,
        );
    }
}
