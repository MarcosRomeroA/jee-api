<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\FindMatch;

use App\Contexts\Shared\Domain\Bus\Query\Query;

final readonly class FindMatchQuery implements Query
{
    public function __construct(
        public string $id
    ) {
    }
}
