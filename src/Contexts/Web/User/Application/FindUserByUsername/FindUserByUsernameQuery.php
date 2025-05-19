<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\FindUserByUsername;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class FindUserByUsernameQuery implements Query
{
    public function __construct(
        public string $username,
    )
    {
    }
}