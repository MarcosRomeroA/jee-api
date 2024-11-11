<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Followers;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Application\Shared\FollowCollectionResponse;

final readonly class UserFollowersQueryHandler implements QueryHandler
{
    public function __construct(
        private UserFollowersFinder $finder
    )
    {
    }

    public function __invoke(UserFollowersQuery $query): FollowCollectionResponse
    {
        $id = new Uuid($query->sessionId);

        return $this->finder->__invoke($id);
    }
}