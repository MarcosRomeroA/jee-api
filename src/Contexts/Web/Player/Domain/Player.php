<?php

declare(strict_types=1);

namespace App\Contexts\Web\Player\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Player\Domain\Events\PlayerCreatedDomainEvent;
use App\Contexts\Web\Player\Domain\ValueObject\UsernameValue;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\Game\Domain\GameRole;
use App\Contexts\Web\Game\Domain\GameRank;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embedded;

#[ORM\Entity(repositoryClass: PlayerRepository::class)]
#[ORM\Table(name: "player")]
class Player extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", length: 36)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[
        ORM\JoinColumn(
            name: "user_id",
            referencedColumnName: "id",
            nullable: false,
        ),
    ]
    private User $user;

    #[ORM\ManyToMany(targetEntity: GameRole::class)]
    #[ORM\JoinTable(name: "player_game_role")]
    #[ORM\JoinColumn(name: "player_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "game_role_id", referencedColumnName: "id")]
    private Collection $gameRoles;

    #[ORM\ManyToOne(targetEntity: GameRank::class)]
    #[
        ORM\JoinColumn(
            name: "game_rank_id",
            referencedColumnName: "id",
            nullable: true,
        ),
    ]
    private ?GameRank $gameRank;

    #[Embedded(class: UsernameValue::class, columnPrefix: false)]
    private UsernameValue $username;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private bool $verified;

    #[ORM\Column(type: "datetime_immutable")]
    private \DateTimeImmutable $createdAt;

    /**
     * @param array<GameRole> $gameRoles
     */
    public function __construct(
        Uuid $id,
        User $user,
        array $gameRoles,
        ?GameRank $gameRank,
        UsernameValue $username,
        bool $verified = false,
    ) {
        $this->id = $id;
        $this->user = $user;
        $this->gameRoles = new ArrayCollection($gameRoles);
        $this->gameRank = $gameRank;
        $this->username = $username;
        $this->verified = $verified;
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * @param array<GameRole> $gameRoles
     */
    public static function create(
        Uuid $id,
        User $user,
        array $gameRoles,
        ?GameRank $gameRank,
        UsernameValue $username,
        bool $verified = false,
    ): self {
        $player = new self(
            $id,
            $user,
            $gameRoles,
            $gameRank,
            $username,
            $verified,
        );

        $player->record(new PlayerCreatedDomainEvent($id));

        return $player;
    }

    /**
     * @param array<GameRole> $gameRoles
     */
    public function update(
        UsernameValue $username,
        array $gameRoles,
        ?GameRank $gameRank,
    ): void {
        $this->username = $username;
        $this->gameRoles = new ArrayCollection($gameRoles);
        $this->gameRank = $gameRank;
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function user(): User
    {
        return $this->user;
    }

    /**
     * @return Collection<int, GameRole>
     */
    public function gameRoles(): Collection
    {
        return $this->gameRoles;
    }

    public function addGameRole(GameRole $gameRole): void
    {
        if (!$this->gameRoles->contains($gameRole)) {
            $this->gameRoles->add($gameRole);
        }
    }

    public function removeGameRole(GameRole $gameRole): void
    {
        $this->gameRoles->removeElement($gameRole);
    }

    public function gameRank(): ?GameRank
    {
        return $this->gameRank;
    }

    public function setGameRank(?GameRank $gameRank): void
    {
        $this->gameRank = $gameRank;
    }

    public function username(): UsernameValue
    {
        return $this->username;
    }

    public function verified(): bool
    {
        return $this->verified;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function verify(): void
    {
        $this->verified = true;
    }
}
