<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\User\Domain\Follow;

class FollowCollectionResponse extends Response
{
    /**
     * @param array<Follow> $follows
     */
    public function __construct(
        private readonly array $follows,
        private readonly string $cdnBaseUrl,
        private readonly bool $isFollower = false,
        private readonly int $limit = 10,
        private readonly int $offset = 0,
        private readonly int $total = 0,
    ) {
    }

    public function toArray(): array
    {
        $data = [];

        foreach ($this->follows as $follow) {
            $data[] = FollowResponse::fromEntity($follow, $this->cdnBaseUrl, $this->isFollower)->toArray();
        }

        return [
            'data' => $data,
            'metadata' => [
                'total' => $this->total,
                'count' => count($data),
                'limit' => $this->limit,
                'offset' => $this->offset,
            ],
        ];
    }
}
