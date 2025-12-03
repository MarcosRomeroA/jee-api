<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Domain\ValueObject\CreatedAtValue;
use App\Contexts\Shared\Domain\ValueObject\UpdatedAtValue;
use App\Contexts\Shared\Domain\Traits\Timestamps;
use App\Contexts\Web\Game\Domain\Game;
use App\Contexts\Web\Team\Domain\Events\RosterCreatedDomainEvent;
use App\Contexts\Web\Team\Domain\Events\RosterUpdatedDomainEvent;
use App\Contexts\Web\Team\Domain\ValueObject\RosterNameValue;
use App\Contexts\Web\Team\Domain\ValueObject\RosterDescriptionValue;
use App\Contexts\Web\Team\Domain\ValueObject\RosterLogoValue;
use App\Contexts\Shared\Infrastructure\Persistence\Doctrine\ContainsNullableEmbeddable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embedded;

#[ContainsNullableEmbeddable]
#[ORM\Entity]
#[ORM\Table(name: 'roster')]
#[ORM\Index(name: 'IDX_ROSTER_TEAM', columns: ['team_id'])]
#[ORM\Index(name: 'IDX_ROSTER_GAME', columns: ['game_id'])]
class Roster extends AggregateRoot
{
    use Timestamps;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', length: 36)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Team::class)]
    #[ORM\JoinColumn(name: 'team_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Team $team;

    #[ORM\ManyToOne(targetEntity: Game::class)]
    #[ORM\JoinColumn(name: 'game_id', referencedColumnName: 'id', nullable: false)]
    private Game $game;

    #[Embedded(class: RosterNameValue::class, columnPrefix: false)]
    private RosterNameValue $name;

    #[Embedded(class: RosterDescriptionValue::class, columnPrefix: false)]
    private RosterDescriptionValue $description;

    #[Embedded(class: RosterLogoValue::class, columnPrefix: false)]
    private RosterLogoValue $logo;

    /**
     * @var Collection<int, RosterPlayer>
     */
    #[ORM\OneToMany(targetEntity: RosterPlayer::class, mappedBy: 'roster', cascade: ['persist', 'remove'])]
    private Collection $rosterPlayers;

    private function __construct(
        Uuid $id,
        Team $team,
        Game $game,
        RosterNameValue $name,
        RosterDescriptionValue $description,
        RosterLogoValue $logo
    ) {
        $this->id = $id;
        $this->team = $team;
        $this->game = $game;
        $this->name = $name;
        $this->description = $description;
        $this->logo = $logo;
        $this->rosterPlayers = new ArrayCollection();
        $this->createdAt = new CreatedAtValue();
        $this->updatedAt = new UpdatedAtValue($this->createdAt->value());
    }

    public static function create(
        Uuid $id,
        Team $team,
        Game $game,
        RosterNameValue $name,
        RosterDescriptionValue $description,
        RosterLogoValue $logo
    ): self {
        $roster = new self($id, $team, $game, $name, $description, $logo);
        $roster->record(new RosterCreatedDomainEvent($id));
        return $roster;
    }

    public function update(
        RosterNameValue $name,
        RosterDescriptionValue $description,
        RosterLogoValue $logo
    ): void {
        $this->name = $name;
        $this->description = $description;
        $this->logo = $logo;
        $this->updatedAt = UpdatedAtValue::now();
        $this->record(new RosterUpdatedDomainEvent($this->id));
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getTeam(): Team
    {
        return $this->team;
    }

    public function getGame(): Game
    {
        return $this->game;
    }

    public function getName(): string
    {
        return $this->name->value();
    }

    public function getDescription(): ?string
    {
        return $this->description->value();
    }

    public function getLogo(): ?string
    {
        return $this->logo->value();
    }

    /**
     * @return Collection<int, RosterPlayer>
     */
    public function getRosterPlayers(): Collection
    {
        return $this->rosterPlayers;
    }

    public function getPlayersCount(): int
    {
        return $this->rosterPlayers->count();
    }

    public function getStartersCount(): int
    {
        $count = 0;
        foreach ($this->rosterPlayers as $rosterPlayer) {
            if ($rosterPlayer->isStarter()) {
                $count++;
            }
        }
        return $count;
    }

    public function hasLeader(): bool
    {
        foreach ($this->rosterPlayers as $rosterPlayer) {
            if ($rosterPlayer->isLeader()) {
                return true;
            }
        }
        return false;
    }

    public function getLeader(): ?RosterPlayer
    {
        foreach ($this->rosterPlayers as $rosterPlayer) {
            if ($rosterPlayer->isLeader()) {
                return $rosterPlayer;
            }
        }
        return null;
    }

    public function addPlayer(RosterPlayer $rosterPlayer): void
    {
        if (!$this->rosterPlayers->contains($rosterPlayer)) {
            $this->rosterPlayers->add($rosterPlayer);
        }
    }

    public function removePlayer(RosterPlayer $rosterPlayer): void
    {
        $this->rosterPlayers->removeElement($rosterPlayer);
    }

    public function hasPlayer(Uuid $playerId): bool
    {
        foreach ($this->rosterPlayers as $rosterPlayer) {
            if ($rosterPlayer->getPlayer()->id()->equals($playerId)) {
                return true;
            }
        }
        return false;
    }

    public function findRosterPlayerByPlayerId(Uuid $playerId): ?RosterPlayer
    {
        foreach ($this->rosterPlayers as $rosterPlayer) {
            if ($rosterPlayer->getPlayer()->id()->equals($playerId)) {
                return $rosterPlayer;
            }
        }
        return null;
    }
}
