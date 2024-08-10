<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Find;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class FindUserQuery implements Query
{
    public function __construct(
        public string $id,
    )
    {
    }
}