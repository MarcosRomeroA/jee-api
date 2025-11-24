<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\Team;
use App\Contexts\Web\Tournament\Domain\Events\TournamentRequestCreatedDomainEvent;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tournament_request')]
class TournamentRequest extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', length: 36)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Tournament::class)]
    #[ORM\JoinColumn(name: 'tournament_id', referencedColumnName: 'id', nullable: false)]
    private Tournament $tournament;

    #[ORM\ManyToOne(targetEntity: Team::class)]
    #[ORM\JoinColumn(name: 'team_id', referencedColumnName: 'id', nullable: false)]
    private Team $team;

    #[ORM\Column(type: 'string', length: 20)]
    private string $status;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    private function __construct(
        Uuid $id,
        Tournament $tournament,
        Team $team
    ) {
        $this->id = $id;
        $this->tournament = $tournament;
        $this->team = $team;
        $this->status = 'pending';
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function create(Uuid $id, Tournament $tournament, Team $team): self
    {
        $request = new self($id, $tournament, $team);
        $request->record(new TournamentRequestCreatedDomainEvent(
            $id,
            $tournament->getId(),
            $team->getId(),
        ));
        return $request;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getTournament(): Tournament
    {
        return $this->tournament;
    }

    public function getTeam(): Team
    {
        return $this->team;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function accept(): void
    {
        $this->status = 'accepted';
    }

    public function reject(): void
    {
        $this->status = 'rejected';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
