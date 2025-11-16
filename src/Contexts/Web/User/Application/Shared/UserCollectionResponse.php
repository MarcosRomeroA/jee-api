<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class UserCollectionResponse extends Response
{
    /** @var UserResponse[] */
    public array $users;
    public int $limit;
    public int $offset;
    public int $total;

    /**
     * @param array<UserResponse> $users
     * @param array $criteria
     * @param int $total
     */
    public function __construct(array $users, array $criteria, int $total = 0)
    {
        $this->users = $users;
        $this->limit = $criteria["limit"] ?? 10;
        $this->offset = $criteria["offset"] ?? 0;
        $this->total = $total;
    }

    public function toArray(): array
    {
        $response["data"] = [];

        foreach ($this->users as $user) {
            $response["data"][] = $user->toArray();
        }

        $response["metadata"] = [
            "limit" => $this->limit,
            "offset" => $this->offset,
            "total" => $this->total,
            "count" => count($this->users),
        ];

        return $response;
    }
}
