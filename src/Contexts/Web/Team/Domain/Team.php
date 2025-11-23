<?php

declare(strict_types=1);

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
use App\Contexts\Web\Team\Domain\ValueObject\TeamNameValue;
use App\Contexts\Web\Team\Domain\ValueObject\TeamDescriptionValue;
use App\Contexts\Web\Team\Domain\ValueObject\TeamImageValue;
use App\Contexts\Shared\Infrastructure\Persistence\Doctrine\ContainsNullableEmbeddable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embedded;

#[ContainsNullableEmbeddable]
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

    #[Embedded(class: TeamNameValue::class, columnPrefix: false)]
    private TeamNameValue $name;

    #[Embedded(class: TeamDescriptionValue::class, columnPrefix: false)]
    private TeamDescriptionValue $description;

    #[Embedded(class: TeamImageValue::class, columnPrefix: false)]
    private TeamImageValue $image;

    /**
     * @var Collection<int, TeamPlayer>
     * @deprecated Use $teamUsers instead
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
     * @var Collection<int, TeamUser>
     */
    #[
        ORM\OneToMany(
            targetEntity: TeamUser::class,
            mappedBy: "team",
            cascade: ["persist", "remove"],
        ),
    ]
    private Collection $teamUsers;

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
        TeamNameValue $name,
        TeamDescriptionValue $description,
        TeamImageValue $image,
        User $creator,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->image = $image;
        $this->creator = $creator;
        $this->leader = $creator;
        $this->teamPlayers = new ArrayCollection();
        $this->teamUsers = new ArrayCollection();
        $this->teamGames = new ArrayCollection();
        $this->createdAt = new CreatedAtValue();
        $this->updatedAt = new UpdatedAtValue($this->createdAt->value());
    }

    public static function create(
        Uuid $id,
        TeamNameValue $name,
        TeamDescriptionValue $description,
        TeamImageValue $image,
        User $creator,
    ): self {
        $team = new self($id, $name, $description, $image, $creator);

        $team->record(new TeamCreatedDomainEvent($id));

        return $team;
    }

    public function update(
        TeamNameValue $name,
        TeamDescriptionValue $description,
        TeamImageValue $image,
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
        return $this->name->value();
    }

    public function description(): ?string
    {
        return $this->description->value();
    }

    public function image(): ?string
    {
        return $this->image->value();
    }

    /**
     * @return Collection<int, TeamPlayer>
     * @deprecated Use teamUsers() instead
     */
    public function teamPlayers(): Collection
    {
        return $this->teamPlayers;
    }

    /**
     * @deprecated Use usersQuantity() instead
     */
    public function playersQuantity(): int
    {
        return $this->teamPlayers->count();
    }

    /**
     * @return Collection<int, TeamUser>
     */
    public function teamUsers(): Collection
    {
        return $this->teamUsers;
    }

    public function usersQuantity(): int
    {
        return $this->teamUsers->count();
    }

    public function isMember(Uuid $userId): bool
    {
        foreach ($this->teamUsers as $teamUser) {
            if ($teamUser->user()->getId()->equals($userId)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return Collection<int, TeamGame>
     */
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
