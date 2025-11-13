<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Application\Search;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class MinePlayersQuery implements Query
{
    public function __construct(
        public string $userId,
        public ?string $query = null,
        public ?int $page = null,
        public ?int $limit = null
    ) {}
}

