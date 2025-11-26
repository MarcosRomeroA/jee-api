<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Auth\Application\Login;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class LoginAdminQuery implements Query
{
    public function __construct(
        public string $user,
        public string $password,
    ) {
    }
}
