<?php declare(strict_types=1);

namespace App\Contexts\Backoffice\Auth\Application\RefreshToken;

use App\Contexts\Backoffice\Auth\Application\Shared\LoginAdminResponse;
use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;

final readonly class RefreshAdminTokenQueryHandler implements QueryHandler
{
    public function __construct(
        private AdminTokenRefresher $refresher,
    ) {
    }

    public function __invoke(RefreshAdminTokenQuery $query): LoginAdminResponse
    {
        return $this->refresher->__invoke($query->refreshToken);
    }
}
