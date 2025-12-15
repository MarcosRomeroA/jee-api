<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Game;
use App\Contexts\Web\Game\Domain\GameRank;
use App\Contexts\Web\Team\Domain\Team;
use App\Contexts\Web\Tournament\Domain\Events\TournamentFinalizedDomainEvent;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Shared\Domain\Moderation\ModerationReason;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TournamentRepository::class)]
#[ORM\Table(name: 'tournament')]
class Tournament extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', length: 36)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Game::class)]
    #[ORM\JoinColumn(name: 'game_id', referencedColumnName: 'id', nullable: false)]
    private Game $game;

    #[ORM\ManyToOne(targetEntity: TournamentStatus::class)]
    #[ORM\JoinColumn(name: 'tournament_status_id', referencedColumnName: 'id', nullable: false)]
    private TournamentStatus $status;

    #[ORM\ManyToOne(targetEntity: GameRank::class)]
    #[ORM\JoinColumn(name: 'min_game_rank_id', referencedColumnName: 'id', nullable: true)]
    private ?GameRank $minGameRank;

    #[ORM\ManyToOne(targetEntity: GameRank::class)]
    #[ORM\JoinColumn(name: 'max_game_rank_id', referencedColumnName: 'id', nullable: true)]
    private ?GameRank $maxGameRank;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'responsible_id', referencedColumnName: 'id', nullable: false)]
    private User $responsible;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'creator_id', referencedColumnName: 'id', nullable: false)]
    private User $creator;

    #[ORM\Column(type: 'string', length: 200)]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $rules;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $registeredTeams;

    #[ORM\Column(type: 'integer')]
    private int $maxTeams;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isOfficial;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $image;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $backgroundImage;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $prize;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $region;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $startAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $endAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $deletedAt;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isDisabled = false;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $disabledAt = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true, enumType: ModerationReason::class)]
    private ?ModerationReason $moderationReason = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $imageUpdatedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $backgroundImageUpdatedAt = null;

    /**
     * @var Collection<int, TournamentTeam>
     */
    #[ORM\OneToMany(targetEntity: TournamentTeam::class, mappedBy: 'tournament', cascade: ['persist', 'remove'])]
    private Collection $tournamentTeams;

    #[ORM\ManyToOne(targetEntity: Team::class)]
    #[ORM\JoinColumn(name: 'first_place_team_id', referencedColumnName: 'id', nullable: true)]
    private ?Team $firstPlaceTeam = null;

    #[ORM\ManyToOne(targetEntity: Team::class)]
    #[ORM\JoinColumn(name: 'second_place_team_id', referencedColumnName: 'id', nullable: true)]
    private ?Team $secondPlaceTeam = null;

    #[ORM\ManyToOne(targetEntity: Team::class)]
    #[ORM\JoinColumn(name: 'third_place_team_id', referencedColumnName: 'id', nullable: true)]
    private ?Team $thirdPlaceTeam = null;

    public function __construct(
        Uuid $id,
        Game $game,
        TournamentStatus $status,
        User $responsible,
        User $creator,
        string $name,
        ?string $description,
        ?string $rules,
        int $maxTeams,
        bool $isOfficial,
        ?string $image,
        ?string $prize,
        ?string $region,
        \DateTimeImmutable $startAt,
        \DateTimeImmutable $endAt,
        ?GameRank $minGameRank = null,
        ?GameRank $maxGameRank = null
    ) {
        $this->id = $id;
        $this->game = $game;
        $this->status = $status;
        $this->responsible = $responsible;
        $this->creator = $creator;
        $this->name = $name;
        $this->description = $description;
        $this->rules = $rules;
        $this->registeredTeams = 0;
        $this->maxTeams = $maxTeams;
        $this->isOfficial = $isOfficial;
        $this->image = $image;
        $this->backgroundImage = null;
        $this->prize = $prize;
        $this->region = $region;
        $this->startAt = $startAt;
        $this->endAt = $endAt;
        $this->minGameRank = $minGameRank;
        $this->maxGameRank = $maxGameRank;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = null;
        $this->deletedAt = null;
        $this->tournamentTeams = new ArrayCollection();
    }

    // Getters
    public function getId(): Uuid
    {
        return $this->id;
    }
    public function getGame(): Game
    {
        return $this->game;
    }
    public function getStatus(): TournamentStatus
    {
        return $this->status;
    }
    public function getMinGameRank(): ?GameRank
    {
        return $this->minGameRank;
    }
    public function getMaxGameRank(): ?GameRank
    {
        return $this->maxGameRank;
    }
    public function getResponsible(): User
    {
        return $this->responsible;
    }
    public function getCreator(): User
    {
        return $this->creator;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function getRules(): ?string
    {
        return $this->rules;
    }
    public function getRegisteredTeams(): int
    {
        return $this->registeredTeams;
    }
    public function getMaxTeams(): int
    {
        return $this->maxTeams;
    }
    public function getIsOfficial(): bool
    {
        return $this->isOfficial;
    }
    public function getImage(): ?string
    {
        return $this->image;
    }
    public function setImage(?string $image): void
    {
        $this->image = $image;
    }
    public function getBackgroundImage(): ?string
    {
        return $this->backgroundImage;
    }
    public function setBackgroundImage(?string $backgroundImage): void
    {
        $this->backgroundImage = $backgroundImage;
    }
    public function getImageUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->imageUpdatedAt;
    }
    public function setImageUpdatedAt(\DateTimeImmutable $imageUpdatedAt): void
    {
        $this->imageUpdatedAt = $imageUpdatedAt;
    }
    /**
     * Gets the public URL for the tournament's image with cache busting.
     *
     * @param string $cdnBaseUrl The CDN base URL
     * @return string|null The full URL with cache-busting version, or null if no image
     */
    public function getImageUrl(string $cdnBaseUrl): ?string
    {
        if ($this->image === null || $this->image === '') {
            return null;
        }

        $path = "jee/tournament/" . $this->id->value() . "/" . $this->image;
        $url = rtrim($cdnBaseUrl, '/') . '/' . $path;

        if ($this->imageUpdatedAt !== null) {
            $url .= '?v=' . $this->imageUpdatedAt->getTimestamp();
        }

        return $url;
    }
    public function getBackgroundImageUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->backgroundImageUpdatedAt;
    }
    public function setBackgroundImageUpdatedAt(\DateTimeImmutable $backgroundImageUpdatedAt): void
    {
        $this->backgroundImageUpdatedAt = $backgroundImageUpdatedAt;
    }
    /**
     * Gets the public URL for the tournament's background image with cache busting.
     *
     * @param string $cdnBaseUrl The CDN base URL
     * @return string|null The full URL with cache-busting version, or null if no background image
     */
    public function getBackgroundImageUrl(string $cdnBaseUrl): ?string
    {
        if ($this->backgroundImage === null || $this->backgroundImage === '') {
            return null;
        }

        $path = "jee/tournament/" . $this->id->value() . "/background/" . $this->backgroundImage;
        $url = rtrim($cdnBaseUrl, '/') . '/' . $path;

        if ($this->backgroundImageUpdatedAt !== null) {
            $url .= '?v=' . $this->backgroundImageUpdatedAt->getTimestamp();
        }

        return $url;
    }
    public function getPrize(): ?string
    {
        return $this->prize;
    }
    public function getRegion(): ?string
    {
        return $this->region;
    }
    public function getStartAt(): \DateTimeImmutable
    {
        return $this->startAt;
    }
    public function getEndAt(): \DateTimeImmutable
    {
        return $this->endAt;
    }
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }
    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }
    public function getTournamentTeams(): Collection
    {
        return $this->tournamentTeams;
    }

    public function getFirstPlaceTeam(): ?Team
    {
        return $this->firstPlaceTeam;
    }

    public function getSecondPlaceTeam(): ?Team
    {
        return $this->secondPlaceTeam;
    }

    public function getThirdPlaceTeam(): ?Team
    {
        return $this->thirdPlaceTeam;
    }

    // Business logic
    public function update(
        string $name,
        ?string $description,
        ?string $rules,
        int $maxTeams,
        bool $isOfficial,
        ?string $image,
        ?string $prize,
        ?string $region,
        \DateTimeImmutable $startAt,
        \DateTimeImmutable $endAt
    ): void {
        $this->name = $name;
        $this->description = $description;
        $this->rules = $rules;
        $this->maxTeams = $maxTeams;
        $this->isOfficial = $isOfficial;
        if ($image !== null) {
            $this->image = $image;
        }
        $this->prize = $prize;
        $this->region = $region;
        $this->startAt = $startAt;
        $this->endAt = $endAt;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function assignResponsible(User $responsible): void
    {
        $this->responsible = $responsible;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function incrementRegisteredTeams(): void
    {
        $this->registeredTeams++;
    }

    public function decrementRegisteredTeams(): void
    {
        if ($this->registeredTeams > 0) {
            $this->registeredTeams--;
        }
    }

    public function delete(): void
    {
        $this->deletedAt = new \DateTimeImmutable();
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    public function isActive(): bool
    {
        return $this->status->isActive() && !$this->isDeleted();
    }

    public function isFinalized(): bool
    {
        return $this->status->isFinalized();
    }

    public function canAcceptTeams(): bool
    {
        return $this->isActive()
            && $this->registeredTeams < $this->maxTeams
            && new \DateTimeImmutable() < $this->startAt;
    }

    public function isResponsible(Uuid $userId): bool
    {
        return $this->responsible->getId()->equals($userId);
    }

    public function hasStarted(): bool
    {
        return new \DateTimeImmutable() >= $this->startAt;
    }

    public function hasEnded(): bool
    {
        return new \DateTimeImmutable() >= $this->endAt;
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

    public function setFinalPositions(
        Team $firstPlace,
        ?Team $secondPlace,
        ?Team $thirdPlace,
        TournamentStatus $finalizedStatus,
    ): void {
        $this->firstPlaceTeam = $firstPlace;
        $this->secondPlaceTeam = $secondPlace;
        $this->thirdPlaceTeam = $thirdPlace;
        $this->status = $finalizedStatus;
        $this->updatedAt = new \DateTimeImmutable();

        $this->record(new TournamentFinalizedDomainEvent(
            $this->id->value(),
            $firstPlace->getId()->value(),
            $secondPlace?->getId()->value(),
            $thirdPlace?->getId()->value(),
        ));
    }

    public function isCreator(Uuid $userId): bool
    {
        return $this->creator->getId()->equals($userId);
    }
}
