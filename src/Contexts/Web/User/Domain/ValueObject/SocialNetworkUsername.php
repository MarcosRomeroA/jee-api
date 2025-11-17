<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class SocialNetworkUsername
{
    #[ORM\Column(type: 'string', length: 255)]
    private string $username;

    public function __construct(string $username)
    {
        $this->ensureIsValid($username);
        $this->username = $username;
    }

    private function ensureIsValid(string $username): void
    {
        if (empty($username)) {
            throw new \InvalidArgumentException('Social network username cannot be empty');
        }

        if (strlen($username) > 255) {
            throw new \InvalidArgumentException('Social network username cannot exceed 255 characters');
        }
    }

    public function value(): string
    {
        return $this->username;
    }

    public function __toString(): string
    {
        return $this->username;
    }
}
