<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

class LikeCollectionResponse extends Response
{
    /**
     * @param array<LikeResponse> $likes
     */
    public function __construct(
        private readonly array $likes,
        private readonly int $limit = 10,
        private readonly int $offset = 0,
        private readonly int $total = 0,
    ) {
    }

    public function toArray(): array
    {
        $data = [];

        foreach ($this->likes as $like) {
            $data[] = $like->toArray();
        }

        return [
            'data' => $data,
            'metadata' => [
                'limit' => $this->limit,
                'offset' => $this->offset,
                'total' => $this->total,
                'count' => count($data),
            ]
        ];
    }
}
