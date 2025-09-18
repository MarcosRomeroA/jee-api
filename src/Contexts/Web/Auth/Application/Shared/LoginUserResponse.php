<?php declare(strict_types=1);

namespace App\Contexts\Web\Auth\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class LoginUserResponse extends Response
{
    public function __construct(
        public string $id,
        public string $notificationToken,
        public string $token,
        public string $refreshToken
    )
    {
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}