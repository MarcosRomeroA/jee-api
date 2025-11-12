<?php declare(strict_types=1);

namespace App\Contexts\Web\Game\Application\Find;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class FindGameQuery implements Query
{
    public function __construct(
        public string $id
    ) {
    }
}

