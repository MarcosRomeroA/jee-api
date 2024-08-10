<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\ValueObject;

use App\Contexts\Shared\Domain\Exception\TextIsLongerThanAllowedException;

abstract class StringValueObject
{
    public function __construct(protected string $value)
    {
    }

    public function value(): string
    {
        return $this->value;
    }

    protected function isEqualTo(StringValueObject|string $other): bool
    {
        if ($other instanceof StringValueObject) {
            return $this->value() === $other->value();
        }

        return $this->value() === $other;
    }

    protected function convertToUppercase(): string
    {
        return strtoupper($this->value());
    }

    protected function convertToLowercase(): string
    {
        return strtolower($this->value());
    }

    protected function convertToTitleCase(): string
    {
        return mb_convert_case($this->value(), MB_CASE_TITLE, "UTF-8");
    }

    protected function limitedToLength(int $length): void
    {
        if (mb_strlen($this->value()) >= $length) {
            throw new TextIsLongerThanAllowedException($length);
        }
    }
}
