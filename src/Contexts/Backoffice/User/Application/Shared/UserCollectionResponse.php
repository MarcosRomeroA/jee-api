<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\User\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class UserCollectionResponse extends Response
{
    public function __construct(
        private readonly array $users,
        private readonly int $total,
        private readonly int $limit,
        private readonly int $offset,
    ) {
    }

    public function toArray(): array
    {
        $data = array_map(
            fn (UserResponse $user) => $user->toArray(),
            $this->users
        );

        return [
            'data' => $data,
            'metadata' => [
                'total' => $this->total,
                'count' => count($data),
                'limit' => $this->limit,
                'offset' => $this->offset,
            ]
        ];
    }
}
