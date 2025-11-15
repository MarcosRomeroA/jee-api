<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Followers;

use App\Contexts\Shared\Domain\FileManager\FileManager;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Application\Shared\FollowCollectionResponse;
use App\Contexts\Web\User\Application\Shared\FollowResponse;
use App\Contexts\Web\User\Application\Shared\UserCollectionMinimalResponse;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Domain\FollowRepository;

final readonly class UserFollowersFinder
{
    public function __construct(
        private UserRepository $userRepository,
        private FileManager $fileManager,
        private FollowRepository $followRepository,
    ) {}

    public function __invoke(
        Uuid $id,
        ?int $limit = null,
        ?int $offset = null,
    ): UserCollectionMinimalResponse {
        $user = $this->userRepository->findById($id);

        $limit = $limit ?? 20;
        $offset = $offset ?? 0;

        $follows = $this->followRepository->findFollowersByUser($user, $limit, $offset);
        $total = $this->followRepository->countFollowersByUser($user);

        $collectionResponse = (new FollowCollectionResponse($follows))->toArray();
        $response = [];

        foreach ($collectionResponse["data"] as $cr) {
            $response[] = new FollowResponse(
                $cr["id"],
                $cr["username"],
                $cr["firstname"],
                $cr["lastname"],
                $this->fileManager->generateTemporaryUrl(
                    "user/profile",
                    $cr["profileImage"],
                ),
            );
        }

        return new UserCollectionMinimalResponse(
            $response,
            $limit,
            $offset,
            $total,
        );
    }
}
