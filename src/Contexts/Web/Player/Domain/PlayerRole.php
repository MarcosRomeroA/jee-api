<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\GameRole;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'player_role')]
class PlayerRole extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', length: 36)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Player::class, inversedBy: 'playerRoles')]
    #[ORM\JoinColumn(name: 'player_id', referencedColumnName: 'id', nullable: false)]
    private Player $player;

    #[ORM\ManyToOne(targetEntity: GameRole::class)]
    #[ORM\JoinColumn(name: 'game_role_id', referencedColumnName: 'id', nullable: false)]
    private GameRole $gameRole;

    public function __construct(
        Uuid $id,
        Player $player,
        GameRole $gameRole
    ) {
        $this->id = $id;
        $this->player = $player;
        $this->gameRole = $gameRole;
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function player(): Player
    {
        return $this->player;
    }

    public function gameRole(): GameRole
    {
        return $this->gameRole;
    }
}
