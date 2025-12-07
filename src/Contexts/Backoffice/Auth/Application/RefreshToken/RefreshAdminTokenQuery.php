<?php declare(strict_types=1);

namespace App\Contexts\Backoffice\Auth\Application\RefreshToken;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class RefreshAdminTokenQuery implements Query
{
    public function __construct(
        public string $refreshToken,
    ) {
    }
}
