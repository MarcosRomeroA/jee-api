<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Game\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Role;
use App\Tests\Unit\Shared\Domain\ValueObject\UuidMother;

final class RoleMother
{
    public static function create(
        ?Uuid $id = null,
        ?string $name = null,
        ?string $description = null
    ): Role {
        return new Role(
            $id ?? UuidMother::create(),
            $name ?? 'Mid',
            $description ?? 'Mid lane player'
        );
    }

    public static function random(): Role
    {
        return self::create();
    }
}

