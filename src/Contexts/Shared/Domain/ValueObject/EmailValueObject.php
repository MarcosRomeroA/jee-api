<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\ValueObject;

use App\Contexts\Shared\Domain\Exception\InvalidEmailException;

abstract class EmailValueObject
{
    public function __construct(protected string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException($this->value);
        }
    }

    public function value(): string
    {
        return $this->value;
    }
}