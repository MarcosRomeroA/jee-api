<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

class UserCollectionMinimalResponse extends Response
{
    /**
     * @param array<FollowResponse> $follows
     */
    public function __construct(
        private readonly array $follows,
        private readonly int $limit = 20,
        private readonly int $offset = 0,
        private readonly int $total = 0,
    ) {}

    public function toArray(): array
    {
        $data = [];

        foreach ($this->follows as $follow) {
            $data[] = $follow->toArray();
        }

        $response["data"] = $data;
        $response["metadata"] = [
            "limit" => $this->limit,
            "offset" => $this->offset,
            "total" => $this->total,
            "count" => count($this->follows),
        ];

        return $response;
    }
}
