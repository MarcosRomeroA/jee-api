<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class PasswordResetTokenValue
{
    #[ORM\Column(name: 'token_hash', type: 'string', length: 64)]
    private string $tokenHash;

    private function __construct(string $tokenHash)
    {
        $this->tokenHash = $tokenHash;
    }

    public static function fromPlainToken(string $plainToken): self
    {
        return new self(hash('sha256', $plainToken));
    }

    public static function fromHash(string $hash): self
    {
        return new self($hash);
    }

    public static function generate(): array
    {
        $plainToken = bin2hex(random_bytes(32));
        $instance = self::fromPlainToken($plainToken);

        return [
            'plain' => $plainToken,
            'instance' => $instance,
        ];
    }

    public function hash(): string
    {
        return $this->tokenHash;
    }

    public function matches(string $plainToken): bool
    {
        return hash_equals($this->tokenHash, hash('sha256', $plainToken));
    }
}
