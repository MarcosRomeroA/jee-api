<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tournament_status')]
class TournamentStatus extends AggregateRoot
{
    // UUID constants (after Version20251119000002.php migration)
    public const string CREATED = 'a50e8400-e29b-41d4-a716-446655440001';
    public const string ACTIVE = 'a50e8400-e29b-41d4-a716-446655440002';
    public const string DELETED = 'a50e8400-e29b-41d4-a716-446655440003';
    public const string ARCHIVED = 'a50e8400-e29b-41d4-a716-446655440004';
    public const string FINALIZED = 'a50e8400-e29b-41d4-a716-446655440005';
    public const string SUSPENDED = 'a50e8400-e29b-41d4-a716-446655440006';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', length: 36)]
    private Uuid $id;

    #[ORM\Column(type: 'string', length: 50)]
    private string $name;

    public function __construct(Uuid $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function isActive(): bool
    {
        return $this->id->value() === self::ACTIVE;
    }

    public function isCreated(): bool
    {
        return $this->id->value() === self::CREATED;
    }

    public function isDeleted(): bool
    {
        return $this->id->value() === self::DELETED;
    }

    public function isFinalized(): bool
    {
        return $this->id->value() === self::FINALIZED;
    }

    public function isSuspended(): bool
    {
        return $this->id->value() === self::SUSPENDED;
    }

    public function isArchived(): bool
    {
        return $this->id->value() === self::ARCHIVED;
    }
}
