<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class SocialNetworkCode
{
    #[ORM\Column(type: 'string', length: 100)]
    private string $code;

    public function __construct(string $code)
    {
        $this->ensureIsValid($code);
        $this->code = $code;
    }

    private function ensureIsValid(string $code): void
    {
        if (empty($code)) {
            throw new \InvalidArgumentException('Social network code cannot be empty');
        }

        if (strlen($code) > 100) {
            throw new \InvalidArgumentException('Social network code cannot exceed 100 characters');
        }

        // Validar formato: solo letras minúsculas, números y guiones bajos
        if (!preg_match('/^[a-z0-9_]+$/', $code)) {
            throw new \InvalidArgumentException('Social network code must contain only lowercase letters, numbers and underscores');
        }
    }

    public function value(): string
    {
        return $this->code;
    }

    public function __toString(): string
    {
        return $this->code;
    }
}
