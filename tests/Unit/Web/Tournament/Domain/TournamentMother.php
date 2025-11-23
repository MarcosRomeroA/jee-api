<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Tournament\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Domain\Tournament;

final class TournamentMother
{
    public static function create(
        ?Uuid $id = null,
        ?string $name = null,
        ?int $maxTeams = null,
        ?bool $isOfficial = null
    ): Tournament {
        $startAt = new \DateTimeImmutable('+1 day');
        $endAt = new \DateTimeImmutable('+7 days');

        return new Tournament(
            $id ?? Uuid::random(),
            GameMother::random(),
            TournamentStatusMother::active(),
            UserMother::random(),
            $name ?? 'Test Tournament',
            'Tournament description',
            'Tournament rules',
            $maxTeams ?? 16,
            $isOfficial ?? false,
            null,
            '$1000 USD',
            'NA',
            $startAt,
            $endAt
        );
    }

    public static function random(): Tournament
    {
        return self::create();
    }

    public static function withName(string $name): Tournament
    {
        return self::create(null, $name);
    }

    public static function official(): Tournament
    {
        return self::create(null, null, null, true);
    }

    public static function withMaxTeams(int $maxTeams): Tournament
    {
        return self::create(null, null, $maxTeams);
    }

    public static function withResponsible(Uuid $responsibleId): Tournament
    {
        $startAt = new \DateTimeImmutable('+1 day');
        $endAt = new \DateTimeImmutable('+7 days');

        return new Tournament(
            Uuid::random(),
            GameMother::random(),
            TournamentStatusMother::active(),
            UserMother::create($responsibleId),
            'Test Tournament',
            'Tournament description',
            'Tournament rules',
            16,
            false,
            null,
            '$1000 USD',
            'NA',
            $startAt,
            $endAt
        );
    }
}
