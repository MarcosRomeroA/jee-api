<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final readonly class UsernameValue
{
    #[ORM\Column(type: 'string', length: 100)]
    private string $username;

    public function __construct(string $username)
    {
        $this->ensureIsValid($username);
        $this->username = $username;
    }

    private function ensureIsValid(string $username): void
    {
        if (empty($username)) {
            throw new \InvalidArgumentException('Username cannot be empty');
        }

        if (strlen($username) > 100) {
            throw new \InvalidArgumentException('Username cannot exceed 100 characters');
        }
    }

    public function value(): string
    {
        return $this->username;
    }

    public function equals(UsernameValue $other): bool
    {
        return $this->username === $other->username;
    }

    public function __toString(): string
    {
        return $this->username;
    }
}

