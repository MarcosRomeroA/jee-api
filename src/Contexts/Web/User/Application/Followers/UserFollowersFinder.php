<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Followers;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Application\Shared\FollowCollectionResponse;
use App\Contexts\Web\User\Domain\UserRepository;

final readonly class UserFollowersFinder
{
    public function __construct(
        private UserRepository $userRepository,
    )
    {
    }

    public function __invoke(Uuid $id): FollowCollectionResponse
    {
        $user = $this->userRepository->findById($id);

        return new FollowCollectionResponse($user->getFollowers()->toArray());
    }
}