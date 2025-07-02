<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\FindUserByUsername;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Web\User\Application\Shared\UserResponse;
use App\Contexts\Web\User\Domain\ValueObject\UsernameValue;

final readonly class FindUserByUsernameQueryHandler implements QueryHandler
{
    public function __construct(
        private UserByUsernameFinder $finder
    )
    {
    }

    public function __invoke(FindUserByUsernameQuery $query): UserResponse
    {
        $username = new UsernameValue($query->username);

        return $this->finder->__invoke($username);
    }
}