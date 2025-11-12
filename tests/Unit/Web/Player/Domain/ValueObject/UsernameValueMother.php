<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Player\Domain\ValueObject;

use App\Contexts\Web\Player\Domain\ValueObject\UsernameValue;

final class UsernameValueMother
{
    public static function create(?string $value = null): UsernameValue
    {
        return new UsernameValue($value ?? self::random());
    }

    public static function random(): string
    {
        return sprintf('player_%s', substr(uniqid(), -8));
    }

    public static function withValue(string $value): UsernameValue
    {
        return new UsernameValue($value);
    }
}

