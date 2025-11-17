<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Domain\Exception;

final class SocialNetworkNotFoundException extends \DomainException
{
    public function __construct(string $socialNetworkId)
    {
        parent::__construct(sprintf('Social network with id "%s" not found', $socialNetworkId));
    }
}
