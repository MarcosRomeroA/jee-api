<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tournament_status')]
class TournamentStatus extends AggregateRoot
{
    public const CREATED = 'created';
    public const ACTIVE = 'active';
    public const DELETED = 'deleted';
    public const ARCHIVED = 'archived';
    public const FINALIZED = 'finalized';
    public const SUSPENDED = 'suspended';

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 50)]
    private string $id;

    #[ORM\Column(type: 'string', length: 50)]
    private string $name;

    public function __construct(string $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function isActive(): bool
    {
        return $this->id === self::ACTIVE;
    }

    public function isCreated(): bool
    {
        return $this->id === self::CREATED;
    }

    public function isDeleted(): bool
    {
        return $this->id === self::DELETED;
    }

    public function isFinalized(): bool
    {
        return $this->id === self::FINALIZED;
    }

    public function isSuspended(): bool
    {
        return $this->id === self::SUSPENDED;
    }

    public function isArchived(): bool
    {
        return $this->id === self::ARCHIVED;
    }
}

