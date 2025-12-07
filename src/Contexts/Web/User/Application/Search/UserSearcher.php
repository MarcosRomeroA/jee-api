<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Search;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Web\User\Application\Shared\UserCollectionResponse;
use App\Contexts\Web\User\Application\Shared\UserResponse;
use App\Contexts\Web\User\Domain\UserRepository;

final readonly class UserSearcher implements QueryHandler
{
    public function __construct(
        private UserRepository $repository,
        private string $cdnBaseUrl,
    )
    {
    }

    public function __invoke(array $criteria): UserCollectionResponse
    {
        $users = $this->repository->searchByCriteria($criteria);

        $response = [];

        foreach ($users as $user){
            $response[] = UserResponse::fromEntity($user, $this->cdnBaseUrl);
        }

        $total = $this->repository->countByCriteria($criteria);

        return new UserCollectionResponse($response, $criteria, $total);
    }
}
