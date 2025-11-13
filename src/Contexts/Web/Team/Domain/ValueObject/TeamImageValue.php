<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain\ValueObject;

use Webmozart\Assert\Assert;

final class TeamImageValue
{
    private ?string $value;

    public function __construct(?string $value)
    {
        if ($value !== null) {
            Assert::maxLength($value, 255, 'Team image URL cannot be longer than 255 characters');
        }

        $this->value = $value;
    }

    public function value(): ?string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value ?? '';
    }
}

