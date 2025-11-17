<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\User\Domain\UserSocialNetwork;

final class UserSocialNetworkCollectionResponse extends Response
{
    /** @var UserSocialNetwork[] */
    public array $userSocialNetworks;

    /**
     * @param array<UserSocialNetwork> $userSocialNetworks
     */
    public function __construct(array $userSocialNetworks)
    {
        $this->userSocialNetworks = $userSocialNetworks;
    }

    public function toArray(): array
    {
        $response = [];

        foreach ($this->userSocialNetworks as $userSocialNetwork) {
            $response[] = UserSocialNetworkResponse::fromEntity($userSocialNetwork)->toArray();
        }

        return [
            'data' => $response
        ];
    }
}
