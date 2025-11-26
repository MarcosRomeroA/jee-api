<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Admin\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class AdminCollectionResponse extends Response
{
    public function __construct(
        private readonly array $admins,
        private readonly array $criteria,
        private readonly int $total = 0
    ) {
    }

    public function toArray(): array
    {
        $data = array_map(
            fn ($admin) => $admin->toArray(),
            $this->admins
        );

        $limit = $this->criteria['limit'] ?? 10;
        $offset = $this->criteria['offset'] ?? 0;

        return [
            'data' => $data,
            'metadata' => [
                'total' => $this->total,
                'count' => count($data),
                'limit' => $limit,
                'offset' => $offset
            ]
        ];
    }
}
