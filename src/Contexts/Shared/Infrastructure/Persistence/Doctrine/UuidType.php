<?php declare(strict_types=1);

namespace App\Contexts\Shared\Infrastructure\Persistence\Doctrine;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class UuidType extends StringType
{
    private function typeClassName(): string
    {
        return Uuid::class;
    }

    final public function getName(): string
    {
        return "uuid";
    }

    final public function convertToPHPValue(
        $value,
        AbstractPlatform $platform,
    ): mixed {
        if ($value === null) {
            return null;
        }

        $className = $this->typeClassName();

        return new $className($value);
    }

    final public function convertToDatabaseValue(
        $value,
        AbstractPlatform $platform,
    ): ?string {
        if ($value === null) {
            return null;
        }

        // Si ya es un string, devolverlo directamente
        if (is_string($value)) {
            return $value;
        }

        /** @var Uuid $value */
        return $value->value();
    }
}
