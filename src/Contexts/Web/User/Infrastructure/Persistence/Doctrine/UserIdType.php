<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Infrastructure\Persistence\Doctrine;

use App\Contexts\Web\Shared\Domain\UuidType;
use App\Contexts\Web\User\Domain\CustomTypes\UserId;

class UserIdType extends UuidType
{
    protected function typeClassName(): string
    {
        return UserId::class;
    }
}