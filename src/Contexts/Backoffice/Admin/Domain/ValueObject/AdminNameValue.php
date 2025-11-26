<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Admin\Domain\ValueObject;

use App\Contexts\Shared\Domain\ValueObject\StringValueObject;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Embeddable]
final class AdminNameValue extends StringValueObject
{
    #[ORM\Column(name: 'name', type: 'string', length: 100)]
    protected string $value;

    public function __construct(string $value)
    {
        $this->validate($value);
        parent::__construct($value);
    }

    private function validate(string $value): void
    {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('Admin name cannot be empty');
        }

        if (strlen($value) < 2) {
            throw new InvalidArgumentException('Admin name must be at least 2 characters long');
        }

        if (strlen($value) > 100) {
            throw new InvalidArgumentException('Admin name cannot exceed 100 characters');
        }
    }
}
