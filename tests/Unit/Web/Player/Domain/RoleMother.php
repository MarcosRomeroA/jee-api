<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Player\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Role;

final class RoleMother
{
    public static function create(
        ?Uuid $id = null,
        ?string $name = null,
        ?string $description = null
    ): Role {
        return new Role(
            $id ?? Uuid::random(),
            $name ?? 'Mid Laner',
            $description ?? 'Middle lane player'
        );
    }

    public static function random(): Role
    {
        return self::create();
    }
}

