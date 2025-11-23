<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Player\Domain\Player;
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

    public function __construct(Uuid $id, Team $team, User $user)
    {
        $this->id = $id;
        $this->team = $team;
        $this->user = $user;
        $this->player = null;
        $this->status = "pending";
        $this->createdAt = new \DateTimeImmutable();
        $this->acceptedAt = null;
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function team(): Team
    {
        return $this->team;
    }

    /**
     * @deprecated Use user() instead
     */
    public function player(): ?Player
    {
        return $this->player;
    }

    public function user(): User
    {
        return $this->user;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function acceptedAt(): ?\DateTimeImmutable
    {
        return $this->acceptedAt;
    }

    public function accept(): void
    {
        $this->status = "accepted";
        $this->acceptedAt = new \DateTimeImmutable();
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
