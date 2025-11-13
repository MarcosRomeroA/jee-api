<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: '`match`')]
class TournamentMatch extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', length: 36)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Tournament::class)]
    #[ORM\JoinColumn(name: 'tournament_id', referencedColumnName: 'id', nullable: false)]
    private Tournament $tournament;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $name;

    #[ORM\Column(type: 'integer')]
    private int $round;

    #[ORM\Column(type: 'string', length: 50)]
    private string $status; // pending, in_progress, completed, cancelled

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $scheduledAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $startedAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $completedAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    /**
     * @var Collection<int, MatchParticipant>
     */
    #[ORM\OneToMany(targetEntity: MatchParticipant::class, mappedBy: 'match', cascade: ['persist', 'remove'])]
    private Collection $participants;

    public function __construct(
        Uuid $id,
        Tournament $tournament,
        int $round,
        ?string $name = null,
        ?\DateTimeImmutable $scheduledAt = null
    ) {
        $this->id = $id;
        $this->tournament = $tournament;
        $this->round = $round;
        $this->name = $name;
        $this->status = 'pending';
        $this->scheduledAt = $scheduledAt;
        $this->startedAt = null;
        $this->completedAt = null;
        $this->createdAt = new \DateTimeImmutable();
        $this->participants = new ArrayCollection();
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function tournament(): Tournament
    {
        return $this->tournament;
    }

    public function name(): ?string
    {
        return $this->name;
    }

    public function round(): int
    {
        return $this->round;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function scheduledAt(): ?\DateTimeImmutable
    {
        return $this->scheduledAt;
    }

    public function startedAt(): ?\DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function completedAt(): ?\DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function participants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(MatchParticipant $participant): void
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
        }
    }

    public function start(): void
    {
        if ($this->status !== 'pending') {
            throw new \DomainException('Match can only be started from pending status');
        }

        $this->status = 'in_progress';
        $this->startedAt = new \DateTimeImmutable();
    }

    public function complete(): void
    {
        if ($this->status !== 'in_progress') {
            throw new \DomainException('Match can only be completed from in_progress status');
        }

        $this->status = 'completed';
        $this->completedAt = new \DateTimeImmutable();
    }

    public function cancel(): void
    {
        if ($this->status === 'completed') {
            throw new \DomainException('Cannot cancel a completed match');
        }

        $this->status = 'cancelled';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function reschedule(\DateTimeImmutable $newScheduledAt): void
    {
        if (!$this->isPending()) {
            throw new \DomainException('Can only reschedule pending matches');
        }

        $this->scheduledAt = $newScheduledAt;
    }
}

