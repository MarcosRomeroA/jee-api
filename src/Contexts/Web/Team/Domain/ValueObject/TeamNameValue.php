<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain\ValueObject;

use Webmozart\Assert\Assert;

final class TeamNameValue
{
    private string $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value, 'Team name cannot be empty');
        Assert::maxLength($value, 100, 'Team name cannot be longer than 100 characters');
        Assert::minLength($value, 3, 'Team name must be at least 3 characters');
        
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

