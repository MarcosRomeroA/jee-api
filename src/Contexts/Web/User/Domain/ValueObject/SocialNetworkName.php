<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class SocialNetworkName
{
    #[ORM\Column(type: 'string', length: 100)]
    private string $name;

    public function __construct(string $name)
    {
        $this->ensureIsValid($name);
        $this->name = $name;
    }

    private function ensureIsValid(string $name): void
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('Social network name cannot be empty');
        }

        if (strlen($name) > 100) {
            throw new \InvalidArgumentException('Social network name cannot exceed 100 characters');
        }
    }

    public function value(): string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
