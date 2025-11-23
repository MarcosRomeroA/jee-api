<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Game;
use App\Contexts\Web\Game\Domain\GameRank;
use App\Contexts\Web\User\Domain\User;
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

    /**
     * @var Collection<int, TournamentTeam>
     */
    #[ORM\OneToMany(targetEntity: TournamentTeam::class, mappedBy: 'tournament', cascade: ['persist', 'remove'])]
    private Collection $tournamentTeams;

    public function __construct(
        Uuid $id,
        Game $game,
        TournamentStatus $status,
        User $responsible,
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
        $this->name = $name;
        $this->description = $description;
        $this->rules = $rules;
        $this->registeredTeams = 0;
        $this->maxTeams = $maxTeams;
        $this->isOfficial = $isOfficial;
        $this->image = $image;
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
    public function id(): Uuid
    {
        return $this->id;
    }
    public function game(): Game
    {
        return $this->game;
    }
    public function status(): TournamentStatus
    {
        return $this->status;
    }
    public function minGameRank(): ?GameRank
    {
        return $this->minGameRank;
    }
    public function maxGameRank(): ?GameRank
    {
        return $this->maxGameRank;
    }
    public function responsible(): User
    {
        return $this->responsible;
    }
    public function name(): string
    {
        return $this->name;
    }
    public function description(): ?string
    {
        return $this->description;
    }
    public function rules(): ?string
    {
        return $this->rules;
    }
    public function registeredTeams(): int
    {
        return $this->registeredTeams;
    }
    public function maxTeams(): int
    {
        return $this->maxTeams;
    }
    public function isOfficial(): bool
    {
        return $this->isOfficial;
    }
    public function image(): ?string
    {
        return $this->image;
    }
    public function prize(): ?string
    {
        return $this->prize;
    }
    public function region(): ?string
    {
        return $this->region;
    }
    public function startAt(): \DateTimeImmutable
    {
        return $this->startAt;
    }
    public function endAt(): \DateTimeImmutable
    {
        return $this->endAt;
    }
    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
    public function updatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }
    public function deletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }
    public function tournamentTeams(): Collection
    {
        return $this->tournamentTeams;
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
        return $this->responsible->id()->equals($userId);
    }

    public function hasStarted(): bool
    {
        return new \DateTimeImmutable() >= $this->startAt;
    }

    public function hasEnded(): bool
    {
        return new \DateTimeImmutable() >= $this->endAt;
    }
}
