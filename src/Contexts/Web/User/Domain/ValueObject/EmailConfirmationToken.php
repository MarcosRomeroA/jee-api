<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final readonly class EmailConfirmationToken
{
    #[ORM\Column(type: 'string', length: 64)]
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

        if (strlen($token) !== 64) {
            throw new \InvalidArgumentException('Email confirmation token must be 64 characters long');
        }
    }

    public function value(): string
    {
        return $this->token;
    }

    public static function generate(): self
    {
        return new self(bin2hex(random_bytes(32)));
    }

    public function equals(EmailConfirmationToken $other): bool
    {
        return $this->token === $other->token;
    }
}

