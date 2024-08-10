<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Find;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Application\Shared\UserResponse;

final readonly class FindUserQueryHandler implements QueryHandler
{
    public function __construct(
        private UserFinder $finder
    )
    {
    }

    public function __invoke(FindUserQuery $query): UserResponse
    {
        $id = new Uuid($query->id);

        return $this->finder->__invoke($id);
    }
}