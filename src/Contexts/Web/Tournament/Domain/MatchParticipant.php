<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\Team;
use App\Contexts\Web\Tournament\Domain\ValueObject\MatchScore;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embedded;

#[ORM\Entity]
#[ORM\Table(name: 'match_participant')]
class MatchParticipant
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', length: 36)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: TournamentMatch::class, inversedBy: 'participants')]
    #[ORM\JoinColumn(name: 'match_id', referencedColumnName: 'id', nullable: false)]
    private TournamentMatch $match;

    #[ORM\ManyToOne(targetEntity: Team::class)]
    #[ORM\JoinColumn(name: 'team_id', referencedColumnName: 'id', nullable: false)]
    private Team $team;

    #[Embedded(class: MatchScore::class, columnPrefix: false)]
    private ?MatchScore $score;

    #[ORM\Column(type: 'integer')]
    private int $position; // Para ordenar participantes (útil en juegos con 3+ jugadores)

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isWinner;

    public function __construct(
        Uuid $id,
        TournamentMatch $match,
        Team $team,
        int $position
    ) {
        $this->id = $id;
        $this->match = $match;
        $this->team = $team;
        $this->position = $position;
        $this->score = null;
        $this->isWinner = false;
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function match(): TournamentMatch
    {
        return $this->match;
    }

    public function team(): Team
    {
        return $this->team;
    }

    public function score(): ?MatchScore
    {
        return $this->score;
    }

    public function position(): int
    {
        return $this->position;
    }

    public function isWinner(): bool
    {
        return $this->isWinner;
    }

    public function setScore(MatchScore $score): void
    {
        $this->score = $score;
    }

    public function markAsWinner(): void
    {
        $this->isWinner = true;
    }

    public function markAsLoser(): void
    {
        $this->isWinner = false;
    }
}

