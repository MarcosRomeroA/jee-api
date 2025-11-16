<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Game;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "team_game")]
#[ORM\UniqueConstraint(name: "UNIQ_TEAM_GAME", columns: ["team_id", "game_id"])]
class TeamGame extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", length: 36)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: "teamGames")]
    #[ORM\JoinColumn(name: "team_id", referencedColumnName: "id", nullable: false)]
    private Team $team;

    #[ORM\ManyToOne(targetEntity: Game::class)]
    #[ORM\JoinColumn(name: "game_id", referencedColumnName: "id", nullable: false)]
    private Game $game;

    #[ORM\Column(type: "datetime_immutable")]
    private \DateTimeImmutable $addedAt;

    public function __construct(
        Uuid $id,
        Team $team,
        Game $game
    ) {
        $this->id = $id;
        $this->team = $team;
        $this->game = $game;
        $this->addedAt = new \DateTimeImmutable();
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function team(): Team
    {
        return $this->team;
    }

    public function game(): Game
    {
        return $this->game;
    }

    public function addedAt(): \DateTimeImmutable
    {
        return $this->addedAt;
    }
}
