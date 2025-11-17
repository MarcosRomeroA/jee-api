<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\User\Domain\SocialNetwork;

final class SocialNetworkCollectionResponse extends Response
{
    /** @var SocialNetwork[] */
    public array $socialNetworks;

    /**
     * @param array<SocialNetwork> $socialNetworks
     */
    public function __construct(array $socialNetworks)
    {
        $this->socialNetworks = $socialNetworks;
    }

    public function toArray(): array
    {
        $response = [];

        foreach ($this->socialNetworks as $socialNetwork) {
            $response[] = SocialNetworkResponse::fromEntity($socialNetwork)->toArray();
        }

        return [
            'data' => $response
        ];
    }
}
