<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\User\Application\Search;

use App\Contexts\Backoffice\User\Application\Shared\UserCollectionResponse;
use App\Contexts\Backoffice\User\Application\Shared\UserResponse;
use App\Contexts\Web\User\Domain\UserRepository;

final readonly class UserSearcher
{
    public function __construct(
        private UserRepository $repository
    ) {
    }

    public function __invoke(array $criteria): UserCollectionResponse
    {
        $users = $this->repository->searchByCriteria($criteria);
        $total = $this->repository->countByCriteria($criteria);

        $responses = [];
        foreach ($users as $user) {
            $responses[] = UserResponse::fromEntity($user);
        }

        $limit = $criteria['limit'] ?? 20;
        $offset = $criteria['offset'] ?? 0;

        return new UserCollectionResponse($responses, $total, $limit, $offset);
    }
}
