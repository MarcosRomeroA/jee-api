<?php declare(strict_types=1);

namespace App\Contexts\Web\Game\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'game_role')]
class GameRole extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', length: 36)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Role::class)]
    #[ORM\JoinColumn(name: 'role_id', referencedColumnName: 'id', nullable: false)]
    private Role $role;

    #[ORM\ManyToOne(targetEntity: Game::class)]
    #[ORM\JoinColumn(name: 'game_id', referencedColumnName: 'id', nullable: false)]
    private Game $game;

    public function __construct(
        Uuid $id,
        Role $role,
        Game $game
    ) {
        $this->id = $id;
        $this->role = $role;
        $this->game = $game;
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function role(): Role
    {
        return $this->role;
    }

    public function game(): Game
    {
        return $this->game;
    }
}

