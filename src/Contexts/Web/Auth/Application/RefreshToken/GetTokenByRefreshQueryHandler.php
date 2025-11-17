<?php

declare(strict_types=1);

namespace App\Contexts\Web\Auth\Application\RefreshToken;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Web\Auth\Application\Shared\LoginUserResponse;

final readonly class GetTokenByRefreshQueryHandler implements QueryHandler
{
    public function __construct(
        private TokenRefresher $refresher
    ) {
    }

    public function __invoke(GetTokenByRefreshQuery $query): LoginUserResponse
    {
        return ($this->refresher)($query->refreshToken);
    }
}
