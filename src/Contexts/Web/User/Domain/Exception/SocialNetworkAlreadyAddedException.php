<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Domain\Exception;

final class SocialNetworkAlreadyAddedException extends \DomainException
{
    public function __construct(string $socialNetworkName)
    {
        parent::__construct(sprintf('Social network "%s" is already added to this user', $socialNetworkName));
    }
}
