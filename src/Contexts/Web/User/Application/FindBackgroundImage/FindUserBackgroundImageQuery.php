<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\FindBackgroundImage;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class FindUserBackgroundImageQuery implements Query
{
    public function __construct(
        public string $userId,
    ) {
    }
}
