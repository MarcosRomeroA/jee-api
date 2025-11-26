<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Auth\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class LoginAdminResponse extends Response
{
    public function __construct(
        private string $id,
        private string $token,
        private string $refreshToken,
        private string $role,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'token' => $this->token,
            'refreshToken' => $this->refreshToken,
            'role' => $this->role,
        ];
    }
}
