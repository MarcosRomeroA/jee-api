<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tournament_status')]
class TournamentStatus extends AggregateRoot
{
    public const string CREATED = 'created';
    public const string ACTIVE = 'active';
    public const string DELETED = 'deleted';
    public const string ARCHIVED = 'archived';
    public const string FINALIZED = 'finalized';
    public const string SUSPENDED = 'suspended';

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

