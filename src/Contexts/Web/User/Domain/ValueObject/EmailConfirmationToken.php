<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid as RamseyUuid;

#[ORM\Embeddable]
final class EmailConfirmationToken
{
    #[ORM\Column(type: 'string', length: 36)]
    private string $token;

    public function __construct(string $token)
    {
        $this->ensureIsValid($token);
        $this->token = $token;
    }

    private function ensureIsValid(string $token): void
    {
        if (empty($token)) {
            throw new \InvalidArgumentException('Email confirmation token cannot be empty');
        }

        if (strlen($token) !== 36) {
            throw new \InvalidArgumentException('Email confirmation token must be 36 characters long (UUID format)');
        }

        // Validar formato UUID
        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $token)) {
            throw new \InvalidArgumentException('Email confirmation token must be a valid UUID v4');
        }
    }

    public function value(): string
    {
        return $this->token;
    }

    public static function generate(): self
    {
        return new self(RamseyUuid::uuid4()->toString());
    }

    public function equals(EmailConfirmationToken $other): bool
    {
        return $this->token === $other->token;
    }
}
