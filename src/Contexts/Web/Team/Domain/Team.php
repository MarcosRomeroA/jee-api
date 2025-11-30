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
use App\Contexts\Shared\Domain\Moderation\ModerationReason;
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

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private bool $isDisabled = false;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    private ?\DateTimeImmutable $disabledAt = null;

    #[ORM\Column(type: "string", length: 50, nullable: true, enumType: ModerationReason::class)]
    private ?ModerationReason $moderationReason = null;

    private function __construct(
        Uuid $id,
        TeamNameValue $name,
        TeamDescriptionValue $description,
        TeamImageValue $image,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->image = $image;
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
        $team = new self($id, $name, $description, $image);

        // Add creator as first member with creator and leader flags
        $teamUser = new TeamUser(Uuid::random(), $team, $creator, true, true);
        $team->teamUsers->add($teamUser);

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

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCreator(): ?User
    {
        foreach ($this->teamUsers as $teamUser) {
            if ($teamUser->isCreator()) {
                return $teamUser->getUser();
            }
        }
        return null;
    }

    public function getLeader(): ?User
    {
        foreach ($this->teamUsers as $teamUser) {
            if ($teamUser->isLeader()) {
                return $teamUser->getUser();
            }
        }
        return null;
    }

    public function getName(): string
    {
        return $this->name->value();
    }

    public function getDescription(): ?string
    {
        return $this->description->value();
    }

    public function getImage(): ?string
    {
        return $this->image->value();
    }

    /**
     * @return Collection<int, TeamPlayer>
     * @deprecated Use getTeamUsers() instead
     */
    public function getTeamPlayers(): Collection
    {
        return $this->teamPlayers;
    }

    /**
     * @deprecated Use getUsersQuantity() instead
     */
    public function getPlayersQuantity(): int
    {
        return $this->teamPlayers->count();
    }

    /**
     * @return Collection<int, TeamUser>
     */
    public function getTeamUsers(): Collection
    {
        return $this->teamUsers;
    }

    public function getUsersQuantity(): int
    {
        return $this->teamUsers->count();
    }

    public function isMember(Uuid $userId): bool
    {
        foreach ($this->teamUsers as $teamUser) {
            if ($teamUser->getUser()->getId()->equals($userId)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return Collection<int, TeamGame>
     */
    public function getTeamGames(): Collection
    {
        return $this->teamGames;
    }

    public function getGamesQuantity(): int
    {
        return $this->teamGames->count();
    }

    public function setLeader(User $newLeader): void
    {
        // Remove leader flag from current leader
        foreach ($this->teamUsers as $teamUser) {
            if ($teamUser->isLeader()) {
                $teamUser->setLeader(false);
            }
        }

        // Check if new leader is already a member
        foreach ($this->teamUsers as $teamUser) {
            if ($teamUser->getUser()->getId()->equals($newLeader->getId())) {
                $teamUser->setLeader(true);
                return;
            }
        }

        // If not a member, add them as leader
        $teamUser = new TeamUser(Uuid::random(), $this, $newLeader, false, true);
        $this->teamUsers->add($teamUser);
    }

    public function isOwner(Uuid $userId): bool
    {
        foreach ($this->teamUsers as $teamUser) {
            if ($teamUser->isCreator() && $teamUser->getUser()->getId()->equals($userId)) {
                return true;
            }
        }
        return false;
    }

    public function isLeader(Uuid $userId): bool
    {
        foreach ($this->teamUsers as $teamUser) {
            if ($teamUser->isLeader() && $teamUser->getUser()->getId()->equals($userId)) {
                return true;
            }
        }
        return false;
    }

    public function canEdit(Uuid $userId): bool
    {
        return $this->isOwner($userId) || $this->isLeader($userId);
    }

    public function addMember(User $user): void
    {
        // Check if user is already a member
        foreach ($this->teamUsers as $teamUser) {
            if ($teamUser->getUser()->getId()->equals($user->getId())) {
                return; // Already a member
            }
        }

        $teamUser = new TeamUser(Uuid::random(), $this, $user, false, false);
        $this->teamUsers->add($teamUser);
    }

    public function removeMember(Uuid $userId): void
    {
        foreach ($this->teamUsers as $teamUser) {
            if ($teamUser->getUser()->getId()->equals($userId)) {
                // Don't allow removing the creator
                if ($teamUser->isCreator()) {
                    return;
                }
                $this->teamUsers->removeElement($teamUser);
                return;
            }
        }
    }

    public function addGame(Game $game): void
    {
        // Check if game already exists
        foreach ($this->teamGames as $teamGame) {
            if ($teamGame->getGame()->getId()->equals($game->getId())) {
                return; // Game already exists, don't add duplicate
            }
        }

        $teamGame = new TeamGame(Uuid::random(), $this, $game);

        $this->teamGames->add($teamGame);
    }

    public function removeGame(Game $game): void
    {
        foreach ($this->teamGames as $teamGame) {
            if ($teamGame->getGame()->getId()->equals($game->getId())) {
                $this->teamGames->removeElement($teamGame);
                return;
            }
        }
    }

    public function hasGame(Game $game): bool
    {
        foreach ($this->teamGames as $teamGame) {
            if ($teamGame->getGame()->getId()->equals($game->getId())) {
                return true;
            }
        }
        return false;
    }

    public function disable(ModerationReason $reason): void
    {
        $this->isDisabled = true;
        $this->moderationReason = $reason;
        $this->disabledAt = new \DateTimeImmutable();
    }

    public function enable(): void
    {
        $this->isDisabled = false;
        $this->moderationReason = null;
        $this->disabledAt = null;
    }

    public function isDisabled(): bool
    {
        return $this->isDisabled;
    }

    public function getModerationReason(): ?ModerationReason
    {
        return $this->moderationReason;
    }

    public function getDisabledAt(): ?\DateTimeImmutable
    {
        return $this->disabledAt;
    }
}
