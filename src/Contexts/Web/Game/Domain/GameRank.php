<?php declare(strict_types=1);

namespace App\Contexts\Web\Game\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'game_rank')]
class GameRank extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', length: 36)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Game::class)]
    #[ORM\JoinColumn(name: 'game_id', referencedColumnName: 'id', nullable: false)]
    private Game $game;

    #[ORM\Column(type: 'string', length: 100)]
    private string $name;

    #[ORM\Column(type: 'integer')]
    private int $level;

    public function __construct(
        Uuid $id,
        Game $game,
        string $name,
        int $level
    ) {
        $this->id = $id;
        $this->game = $game;
        $this->name = $name;
        $this->level = $level;
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function game(): Game
    {
        return $this->game;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function level(): int
    {
        return $this->level;
    }
}

