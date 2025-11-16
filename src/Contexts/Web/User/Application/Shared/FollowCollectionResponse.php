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
        private readonly bool $isFollower = false
    ) {
    }

    public function toArray(): array
    {
        $data = [];

        foreach ($this->follows as $follow) {
            $data[] = FollowResponse::fromEntity($follow, $this->isFollower)->toArray();
        }

        $response["data"] = $data;
        $response["metadata"]["quantity"] = count($this->follows);

        return $response;
    }
}
