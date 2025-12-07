<?php declare(strict_types=1);

namespace App\Contexts\Web\Auth\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\User\Application\Shared\UserPreferenceResponse;

final class LoginUserResponse extends Response
{
    public function __construct(
        public string $id,
        public string $notificationToken,
        public string $token,
        public string $refreshToken,
        public UserPreferenceResponse $preferences,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'notificationToken' => $this->notificationToken,
            'token' => $this->token,
            'refreshToken' => $this->refreshToken,
            'preferences' => $this->preferences->toArray(),
        ];
    }
}
