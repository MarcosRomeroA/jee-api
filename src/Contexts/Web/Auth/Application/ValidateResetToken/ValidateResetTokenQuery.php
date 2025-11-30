<?php

declare(strict_types=1);

namespace App\Contexts\Web\Auth\Application\ValidateResetToken;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class ValidateResetTokenQuery implements Query
{
    public function __construct(
        public string $token,
    ) {
    }
}
