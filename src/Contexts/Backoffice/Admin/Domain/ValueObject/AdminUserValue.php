<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Admin\Domain\ValueObject;

use App\Contexts\Shared\Domain\ValueObject\StringValueObject;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Embeddable]
final class AdminUserValue extends StringValueObject
{
    #[ORM\Column(name: 'user', type: 'string', length: 50, unique: true)]
    protected string $value;

    public function __construct(string $value)
    {
        $this->validate($value);
        parent::__construct($value);
    }

    private function validate(string $value): void
    {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('Admin user cannot be empty');
        }

        if (strlen($value) < 3) {
            throw new InvalidArgumentException('Admin user must be at least 3 characters long');
        }

        if (strlen($value) > 50) {
            throw new InvalidArgumentException('Admin user cannot exceed 50 characters');
        }

        if (!preg_match('/^[a-zA-Z0-9_.-]+$/', $value)) {
            throw new InvalidArgumentException('Admin user can only contain letters, numbers, dots, hyphens and underscores');
        }
    }
}
