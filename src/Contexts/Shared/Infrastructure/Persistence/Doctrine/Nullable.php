<?php declare(strict_types=1);

namespace App\Contexts\Shared\Infrastructure\Persistence\Doctrine;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Nullable
{
}