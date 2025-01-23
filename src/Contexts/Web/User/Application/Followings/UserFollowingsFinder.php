<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Followings;

use App\Contexts\Shared\Domain\FileManager\FileManager;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Application\Shared\FollowCollectionResponse;
use App\Contexts\Web\User\Application\Shared\FollowResponse;
use App\Contexts\Web\User\Application\Shared\UseCollectionMinimalResponse;
use App\Contexts\Web\User\Domain\UserRepository;

final readonly class UserFollowingsFinder
{
    public function __construct(
        private UserRepository $userRepository,
        private FileManager $fileManager
    )
    {
    }

    public function __invoke(Uuid $id): UseCollectionMinimalResponse
    {
        $user = $this->userRepository->findById($id);

        $collectionResponse = (new FollowCollectionResponse($user->getFollowings()->toArray()))->toArray();

        $response = [];

        /**
         * @var FollowResponse $cr
         */
        foreach ($collectionResponse['data'] as $cr){
            $response[] = new FollowResponse(
                $cr->id,
                $cr->username,
                $cr->firstname,
                $cr->lastname,
                $this->fileManager->generateTemporaryUrl('user/profile', $cr->profileImage)
            );
        }

        return new UseCollectionMinimalResponse($response);
    }
}