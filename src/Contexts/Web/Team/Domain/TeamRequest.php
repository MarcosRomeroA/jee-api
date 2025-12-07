<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Player\Domain\Player;
use App\Contexts\Web\Team\Domain\Events\TeamRequestAcceptedDomainEvent;
use App\Contexts\Web\Team\Domain\Events\TeamRequestCreatedDomainEvent;
use App\Contexts\Web\User\Domain\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "team_request")]
class TeamRequest extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", length: 36)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Team::class)]
    #[
        ORM\JoinColumn(
            name: "team_id",
            referencedColumnName: "id",
            nullable: false,
        ),
    ]
    private Team $team;

    /**
     * @deprecated Use $user instead
     */
    #[ORM\ManyToOne(targetEntity: Player::class)]
    #[
        ORM\JoinColumn(
            name: "player_id",
            referencedColumnName: "id",
            nullable: true,
        ),
    ]
    private ?Player $player = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[
        ORM\JoinColumn(
            name: "user_id",
            referencedColumnName: "id",
            nullable: false,
        ),
    ]
    private User $user;

    #[ORM\Column(type: "string", length: 20)]
    private string $status;

    #[ORM\Column(type: "datetime_immutable")]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    private ?\DateTimeImmutable $acceptedAt;

    private function __construct(Uuid $id, Team $team, User $user)
    {
        $this->id = $id;
        $this->team = $team;
        $this->user = $user;
        $this->player = null;
        $this->status = "pending";
        $this->createdAt = new \DateTimeImmutable();
        $this->acceptedAt = null;
    }

    public static function create(Uuid $id, Team $team, User $user): self
    {
        $request = new self($id, $team, $user);
        $request->record(new TeamRequestCreatedDomainEvent(
            $id,
            $team->getId(),
            $user->getId(),
        ));
        return $request;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getTeam(): Team
    {
        return $this->team;
    }

    /**
     * @deprecated Use getUser() instead
     */
    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getAcceptedAt(): ?\DateTimeImmutable
    {
        return $this->acceptedAt;
    }

    public function accept(): void
    {
        $this->status = "accepted";
        $this->acceptedAt = new \DateTimeImmutable();
        $this->record(new TeamRequestAcceptedDomainEvent(
            $this->id,
            $this->team->getId(),
            $this->user->getId(),
        ));
    }

    public function reject(): void
    {
        $this->status = "rejected";
    }

    public function isPending(): bool
    {
        return $this->status === "pending";
    }
}
