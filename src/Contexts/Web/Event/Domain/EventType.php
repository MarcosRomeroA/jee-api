<?php

declare(strict_types=1);

namespace App\Contexts\Web\Event\Domain;

enum EventType: string
{
    case PRESENCIAL = 'presencial';
    case VIRTUAL = 'virtual';

    public function label(): string
    {
        return match ($this) {
            self::PRESENCIAL => 'Presencial',
            self::VIRTUAL => 'Virtual',
        };
    }

    public static function values(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }
}
