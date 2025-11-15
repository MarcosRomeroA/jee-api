<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Followings;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class UserFollowingsQuery implements Query
{
    public function __construct(
        public string $id,
        public string $sessionId,
        public ?int $limit = null,
        public ?int $offset = null,
    ) {}
}
