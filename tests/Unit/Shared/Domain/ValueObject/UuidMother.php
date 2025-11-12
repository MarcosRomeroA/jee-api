<?php declare(strict_types=1);

namespace App\Tests\Unit\Shared\Domain\ValueObject;

use App\Contexts\Shared\Domain\ValueObject\Uuid;

final class UuidMother
{
    public static function create(?string $value = null): Uuid
    {
        return new Uuid($value ?? self::random());
    }

    public static function random(): string
    {
        return \Ramsey\Uuid\Uuid::uuid4()->toString();
    }
}

