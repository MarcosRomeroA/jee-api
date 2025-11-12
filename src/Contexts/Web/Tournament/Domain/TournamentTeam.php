<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\Team;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tournament_team')]
class TournamentTeam extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', length: 36)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Tournament::class, inversedBy: 'tournamentTeams')]
    #[ORM\JoinColumn(name: 'tournament_id', referencedColumnName: 'id', nullable: false)]
    private Tournament $tournament;

    #[ORM\ManyToOne(targetEntity: Team::class)]
    #[ORM\JoinColumn(name: 'team_id', referencedColumnName: 'id', nullable: false)]
    private Team $team;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeImmutable $registeredAt;

    public function __construct(
        Uuid $id,
        Tournament $tournament,
        Team $team
    ) {
        $this->id = $id;
        $this->tournament = $tournament;
        $this->team = $team;
        $this->registeredAt = new \DateTimeImmutable();
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function tournament(): Tournament
    {
        return $this->tournament;
    }

    public function team(): Team
    {
        return $this->team;
    }

    public function registeredAt(): \DateTimeImmutable
    {
        return $this->registeredAt;
    }
}

