<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Domain\Exception;

final class UserSocialNetworkNotFoundException extends \DomainException
{
    public function __construct(string $userSocialNetworkId)
    {
        parent::__construct(sprintf('User social network with id "%s" not found', $userSocialNetworkId));
    }
}
