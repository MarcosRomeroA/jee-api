<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Tournament\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Domain\TournamentStatus;

final class TournamentStatusMother
{
    public static function create(?Uuid $id = null, ?string $name = null): TournamentStatus
    {
        $statusId = $id ?? Uuid::random();
        $statusName = $name ?? 'active';

        $status = new class($statusId, $statusName) extends TournamentStatus {
            public function __construct(Uuid $id, string $name)
            {
                $reflection = new \ReflectionClass(TournamentStatus::class);

                $idProperty = $reflection->getProperty('id');
                $idProperty->setAccessible(true);
                $idProperty->setValue($this, $id);

                $nameProperty = $reflection->getProperty('name');
                $nameProperty->setAccessible(true);
                $nameProperty->setValue($this, $name);
            }
        };

        return $status;
    }

    public static function random(): TournamentStatus
    {
        return self::create();
    }

    public static function active(): TournamentStatus
    {
        return self::create(null, 'active');
    }

    public static function created(): TournamentStatus
    {
        return self::create(null, 'created');
    }
}

