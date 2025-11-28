<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Hashtag\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class HashtagCollectionResponse extends Response
{
    /**
     * @param HashtagResponse[] $hashtags
     */
    public function __construct(
        private readonly array $hashtags,
        private readonly int $total,
        private readonly int $limit,
        private readonly int $offset,
    ) {
    }

    public function toArray(): array
    {
        return [
            'data' => array_map(fn (HashtagResponse $hashtag) => $hashtag->toArray(), $this->hashtags),
            'metadata' => [
                'total' => $this->total,
                'count' => count($this->hashtags),
                'limit' => $this->limit,
                'offset' => $this->offset,
            ],
        ];
    }
}
