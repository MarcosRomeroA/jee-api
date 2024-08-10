<?php declare(strict_types=1);

namespace App\Contexts\Web\Auth\Application\RefreshToken;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

readonly class GetTokenByRefreshQuery implements Query
{
    public function __construct(
        public string $refreshToken,
    )
    {
    }
}