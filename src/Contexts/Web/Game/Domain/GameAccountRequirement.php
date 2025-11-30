<?php

declare(strict_types=1);

namespace App\Contexts\Web\Game\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "game_account_requirement")]
class GameAccountRequirement extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", length: 36)]
    private Uuid $id;

    #[ORM\OneToOne(targetEntity: Game::class)]
    #[ORM\JoinColumn(name: "game_id", referencedColumnName: "id", unique: true, nullable: false)]
    private Game $game;

    #[ORM\Column(type: "json")]
    private array $requirements;

    private function __construct(
        Uuid $id,
        Game $game,
        array $requirements,
    ) {
        $this->id = $id;
        $this->game = $game;
        $this->requirements = $requirements;
    }

    public static function create(
        Uuid $id,
        Game $game,
        array $requirements,
    ): self {
        return new self($id, $game, $requirements);
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getGame(): Game
    {
        return $this->game;
    }

    public function getRequirements(): array
    {
        return $this->requirements;
    }
}
