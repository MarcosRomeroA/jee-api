<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Followings;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Application\Shared\FollowCollectionResponse;

final readonly class UserFollowingsQueryHandler implements QueryHandler
{
    public function __construct(private UserFollowingsFinder $finder) {}

    public function __invoke(
        UserFollowingsQuery $query,
    ): FollowCollectionResponse {
        $id = new Uuid($query->id);

        return $this->finder->__invoke($id, $query->limit, $query->offset);
    }
}
