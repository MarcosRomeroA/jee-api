<?php declare(strict_types=1);

namespace App\Contexts\Shared\Infrastructure\Persistence\Doctrine;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class ContainsNullableEmbeddable
{
}