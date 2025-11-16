<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Domain\ValueObject\CreatedAtValue;
use App\Contexts\Shared\Domain\ValueObject\UpdatedAtValue;
use App\Contexts\Shared\Domain\Traits\Timestamps;
use App\Contexts\Web\Game\Domain\Game;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\Team\Domain\Events\TeamCreatedDomainEvent;
use App\Contexts\Web\Team\Domain\Events\TeamUpdatedDomainEvent;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
#[ORM\Table(name: "team")]
class Team extends AggregateRoot
{
    use Timestamps;

    #[ORM\Id]
    #[ORM\Column(type: "uuid", length: 36)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[
        ORM\JoinColumn(
            name: "creator_id",
            referencedColumnName: "id",
            nullable: false,
        ),
    ]
    private User $creator;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[
        ORM\JoinColumn(
            name: "leader_id",
            referencedColumnName: "id",
            nullable: false,
        ),
    ]
    private User $leader;

    #[ORM\Column(type: "string", length: 100)]
    private string $name;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $image = null;

    /**
     * @var Collection<int, TeamPlayer>
     */
    #[
        ORM\OneToMany(
            targetEntity: TeamPlayer::class,
            mappedBy: "team",
            cascade: ["persist", "remove"],
        ),
    ]
    private Collection $teamPlayers;

    /**
     * @var Collection<int, TeamGame>
     */
    #[
        ORM\OneToMany(
            targetEntity: TeamGame::class,
            mappedBy: "team",
            cascade: ["persist", "remove"],
        ),
    ]
    private Collection $teamGames;

    public function __construct(
        Uuid $id,
        string $name,
        ?string $description,
        ?string $image,
        User $creator,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->image = $image;
        $this->creator = $creator;
        $this->leader = $creator;
        $this->teamPlayers = new ArrayCollection();
        $this->teamGames = new ArrayCollection();
        $this->createdAt = new CreatedAtValue();
        $this->updatedAt = new UpdatedAtValue($this->createdAt->value());
    }

    public static function create(
        Uuid $id,
        string $name,
        ?string $description,
        ?string $image,
        User $creator,
    ): self {
        $team = new self($id, $name, $description, $image, $creator);

        $team->record(new TeamCreatedDomainEvent($id));

        return $team;
    }

    public function update(
        string $name,
        ?string $description,
        ?string $image,
    ): void {
        $this->name = $name;
        $this->description = $description;
        $this->image = $image;

        $this->record(new TeamUpdatedDomainEvent($this->id));
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function creator(): User
    {
        return $this->creator;
    }

    public function leader(): User
    {
        return $this->leader;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function image(): ?string
    {
        return $this->image;
    }

    public function teamPlayers(): Collection
    {
        return $this->teamPlayers;
    }

    public function playersQuantity(): int
    {
        return $this->teamPlayers->count();
    }

    public function teamGames(): Collection
    {
        return $this->teamGames;
    }

    public function gamesQuantity(): int
    {
        return $this->teamGames->count();
    }

    public function setLeader(User $leader): void
    {
        $this->leader = $leader;
    }

    public function isOwner(Uuid $userId): bool
    {
        return $this->creator->getId()->equals($userId);
    }

    public function isLeader(Uuid $userId): bool
    {
        return $this->leader->getId()->equals($userId);
    }

    public function addGame(Game $game): void
    {
        // Check if game already exists
        foreach ($this->teamGames as $teamGame) {
            if ($teamGame->game()->getId()->equals($game->getId())) {
                return; // Game already exists, don't add duplicate
            }
        }

        $teamGame = new TeamGame(Uuid::random(), $this, $game);

        $this->teamGames->add($teamGame);
    }

    public function removeGame(Game $game): void
    {
        foreach ($this->teamGames as $teamGame) {
            if ($teamGame->game()->getId()->equals($game->getId())) {
                $this->teamGames->removeElement($teamGame);
                return;
            }
        }
    }

    public function hasGame(Game $game): bool
    {
        foreach ($this->teamGames as $teamGame) {
            if ($teamGame->game()->getId()->equals($game->getId())) {
                return true;
            }
        }
        return false;
    }
}
