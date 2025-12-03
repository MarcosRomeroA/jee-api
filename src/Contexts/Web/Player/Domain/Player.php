<?php

declare(strict_types=1);

namespace App\Contexts\Web\Player\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Player\Domain\Events\PlayerCreatedDomainEvent;
use App\Contexts\Web\Player\Domain\ValueObject\GameAccountDataValue;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\Game\Domain\Game;
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

    #[ORM\ManyToOne(targetEntity: Game::class)]
    #[
        ORM\JoinColumn(
            name: "game_id",
            referencedColumnName: "id",
            nullable: false,
        ),
    ]
    private Game $game;

    #[ORM\ManyToMany(targetEntity: GameRole::class)]
    #[ORM\JoinTable(name: "player_game_role")]
    #[ORM\JoinColumn(name: "player_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "game_role_id", referencedColumnName: "id")]
    private Collection $gameRoles;

    /**
     * @deprecated Use accountData instead. This field will always be null for new players.
     */
    #[ORM\ManyToOne(targetEntity: GameRank::class)]
    #[
        ORM\JoinColumn(
            name: "game_rank_id",
            referencedColumnName: "id",
            nullable: true,
        ),
    ]
    private ?GameRank $gameRank;

    #[Embedded(class: GameAccountDataValue::class, columnPrefix: false)]
    private GameAccountDataValue $accountData;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private bool $verified;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    private ?\DateTimeImmutable $verifiedAt;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private bool $isOwnershipVerified;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    private ?\DateTimeImmutable $ownershipVerifiedAt;

    #[ORM\Column(type: "datetime_immutable")]
    private \DateTimeImmutable $createdAt;

    /**
     * @param array<GameRole> $gameRoles
     */
    private function __construct(
        Uuid $id,
        User $user,
        Game $game,
        array $gameRoles,
        GameAccountDataValue $accountData,
        bool $verified = false,
    ) {
        $this->id = $id;
        $this->user = $user;
        $this->game = $game;
        $this->gameRoles = new ArrayCollection($gameRoles);
        $this->gameRank = null;
        $this->accountData = $accountData;
        $this->verified = $verified;
        $this->verifiedAt = null;
        $this->isOwnershipVerified = false;
        $this->ownershipVerifiedAt = null;
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * @param array<GameRole> $gameRoles
     */
    public static function create(
        Uuid $id,
        User $user,
        Game $game,
        array $gameRoles,
        GameAccountDataValue $accountData,
        bool $verified = false,
    ): self {
        $player = new self(
            $id,
            $user,
            $game,
            $gameRoles,
            $accountData,
            $verified,
        );

        $player->record(new PlayerCreatedDomainEvent($id));

        return $player;
    }

    /**
     * @param array<GameRole> $gameRoles
     */
    public function update(
        array $gameRoles,
        GameAccountDataValue $accountData,
    ): void {
        $this->gameRoles = new ArrayCollection($gameRoles);
        $this->accountData = $accountData;
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function user(): User
    {
        return $this->user;
    }

    public function game(): Game
    {
        return $this->game;
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

    /**
     * Returns the display username from accountData.
     * For Riot games: returns username field
     * For Steam games: returns steamId
     */
    public function username(): ?string
    {
        // For Riot games, return the username
        if ($this->accountData->username() !== null) {
            return $this->accountData->username();
        }

        // For Steam games, return the steamId
        return $this->accountData->steamId();
    }

    public function accountData(): GameAccountDataValue
    {
        return $this->accountData;
    }

    public function verified(): bool
    {
        return $this->verified;
    }

    public function verifiedAt(): ?\DateTimeImmutable
    {
        return $this->verifiedAt;
    }

    public function isOwnershipVerified(): bool
    {
        return $this->isOwnershipVerified;
    }

    public function ownershipVerifiedAt(): ?\DateTimeImmutable
    {
        return $this->ownershipVerifiedAt;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function verify(): void
    {
        $this->verified = true;
        $this->verifiedAt = new \DateTimeImmutable();
    }

    public function verifyOwnership(): void
    {
        $this->isOwnershipVerified = true;
        $this->ownershipVerifiedAt = new \DateTimeImmutable();
    }
}
