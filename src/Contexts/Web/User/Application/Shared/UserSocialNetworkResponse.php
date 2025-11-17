<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\User\Domain\UserSocialNetwork;

final class UserSocialNetworkResponse extends Response
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $code,
        public readonly string $url,
        public readonly string $username,
        public readonly string $fullUrl
    ) {
    }

    public static function fromEntity(UserSocialNetwork $userSocialNetwork): self
    {
        $socialNetwork = $userSocialNetwork->socialNetwork();
        $username = $userSocialNetwork->username()->value();
        $baseUrl = $socialNetwork->url()->value();

        // Build full URL: simply concatenate baseUrl + username
        // baseUrl already includes the proper separator (/ or /@)
        $fullUrl = $baseUrl . $username;

        return new self(
            $socialNetwork->id()->value(),
            $socialNetwork->name()->value(),
            $socialNetwork->code()->value(),
            $baseUrl,
            $username,
            $fullUrl
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
