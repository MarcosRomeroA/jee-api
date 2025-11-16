<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Team\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\Team;
use App\Contexts\Web\Team\Domain\ValueObject\TeamNameValue;
use App\Contexts\Web\Team\Domain\ValueObject\TeamDescriptionValue;
use App\Contexts\Web\Team\Domain\ValueObject\TeamImageValue;
use App\Contexts\Web\User\Domain\User;

final class TeamMother
{
    public static function create(
        ?Uuid $id = null,
        ?string $name = null,
        ?string $description = null,
        ?string $image = null,
        ?User $creator = null,
    ): Team {
        return Team::create(
            $id ?? Uuid::random(),
            new TeamNameValue($name ?? "Test Team"),
            new TeamDescriptionValue($description ?? "Test team description"),
            new TeamImageValue($image),
            $creator ?? UserMother::random(),
        );
    }

    public static function random(): Team
    {
        return self::create();
    }

    public static function withName(string $name): Team
    {
        return self::create(null, $name);
    }

    public static function withImage(string $image): Team
    {
        return self::create(null, null, null, $image);
    }

    public static function withCreator(Uuid $creatorId): Team
    {
        return self::create(
            null,
            null,
            null,
            null,
            UserMother::create($creatorId),
        );
    }

    public static function withLeader(Uuid $leaderId): Team
    {
        $team = self::create();
        $team->setLeader(UserMother::create($leaderId));
        return $team;
    }
}
