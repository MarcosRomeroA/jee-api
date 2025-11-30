<?php

declare(strict_types=1);

namespace App\Contexts\Web\Auth\Application\ValidateResetToken;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class ValidateResetTokenResponse extends Response
{
    public function __construct(
        private readonly bool $valid,
        private readonly ?string $email,
    ) {
    }

    public function toArray(): array
    {
        return [
            'valid' => $this->valid,
            'email' => $this->email,
        ];
    }
}
