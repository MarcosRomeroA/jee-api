<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\ValueObject;

use DateTime;

abstract class DatetimeValueObject
{
    public function __construct(protected ?DateTime $value)
    {
    }

    public function value(): ?Datetime
    {
        return $this->value;
    }
}