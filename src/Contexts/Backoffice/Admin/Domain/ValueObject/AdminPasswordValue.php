<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Admin\Domain\ValueObject;

use App\Contexts\Shared\Domain\ValueObject\StringValueObject;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Embeddable]
final class AdminPasswordValue extends StringValueObject
{
    #[ORM\Column(name: 'password', type: 'string', length: 255)]
    protected string $value;

    public function __construct(string $value)
    {
        $this->validate($value);
        $this->value = $this->isHashed($value) ? $value : $this->hashPassword($value);
    }

    private function validate(string $value): void
    {
        if ($this->isHashed($value)) {
            return;
        }

        if (empty(trim($value))) {
            throw new InvalidArgumentException('Admin password cannot be empty');
        }

        if (strlen($value) < 8) {
            throw new InvalidArgumentException('Admin password must be at least 8 characters long');
        }
    }

    private function isHashed(string $value): bool
    {
        return str_starts_with($value, '$2y$') || str_starts_with($value, '$2a$');
    }

    private function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public function verifyPassword(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->value);
    }
}
