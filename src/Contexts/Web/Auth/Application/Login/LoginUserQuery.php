<?php declare(strict_types=1);

namespace App\Contexts\Web\Auth\Application\Login;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class LoginUserQuery implements Query
{
    public function __construct(
        public string $email,
        public string $password,
    )
    {
    }
}