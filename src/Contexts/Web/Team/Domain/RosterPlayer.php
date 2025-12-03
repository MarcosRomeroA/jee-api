<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\GameRole;
use App\Contexts\Web\Player\Domain\Player;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'roster_player')]
#[ORM\UniqueConstraint(name: 'UNIQ_ROSTER_PLAYER', columns: ['roster_id', 'player_id'])]
#[ORM\Index(name: 'IDX_ROSTER_PLAYER_STARTER', columns: ['is_starter'])]
#[ORM\Index(name: 'IDX_ROSTER_PLAYER_LEADER', columns: ['is_leader'])]
class RosterPlayer extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', length: 36)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Roster::class, inversedBy: 'rosterPlayers')]
    #[ORM\JoinColumn(name: 'roster_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Roster $roster;

    #[ORM\ManyToOne(targetEntity: Player::class)]
    #[ORM\JoinColumn(name: 'player_id', referencedColumnName: 'id', nullable: false)]
    private Player $player;

    #[ORM\ManyToOne(targetEntity: GameRole::class)]
    #[ORM\JoinColumn(name: 'game_role_id', referencedColumnName: 'id', nullable: true)]
    private ?GameRole $gameRole;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isStarter = false;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isLeader = false;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        Uuid $id,
        Roster $roster,
        Player $player,
        bool $isStarter = false,
        bool $isLeader = false,
        ?GameRole $gameRole = null
    ) {
        $this->id = $id;
        $this->roster = $roster;
        $this->player = $player;
        $this->isStarter = $isStarter;
        $this->isLeader = $isLeader;
        $this->gameRole = $gameRole;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function update(
        bool $isStarter,
        bool $isLeader,
        ?GameRole $gameRole
    ): void {
        $this->isStarter = $isStarter;
        $this->isLeader = $isLeader;
        $this->gameRole = $gameRole;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getRoster(): Roster
    {
        return $this->roster;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function getGameRole(): ?GameRole
    {
        return $this->gameRole;
    }

    public function isStarter(): bool
    {
        return $this->isStarter;
    }

    public function isLeader(): bool
    {
        return $this->isLeader;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setStarter(bool $isStarter): void
    {
        $this->isStarter = $isStarter;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function setLeader(bool $isLeader): void
    {
        $this->isLeader = $isLeader;
        $this->updatedAt = new \DateTimeImmutable();
    }
}
