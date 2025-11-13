<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Player\Domain\ValueObject\UsernameValue;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\Game\Domain\GameRole;
use App\Contexts\Web\Game\Domain\GameRank;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embedded;

#[ORM\Entity(repositoryClass: PlayerRepository::class)]
#[ORM\Table(name: 'player')]
class Player extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', length: 36)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private User $user;

    #[ORM\OneToMany(targetEntity: PlayerRole::class, mappedBy: 'player', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $playerRoles;

    #[ORM\ManyToOne(targetEntity: GameRank::class)]
    #[ORM\JoinColumn(name: 'game_rank_id', referencedColumnName: 'id', nullable: true)]
    private ?GameRank $gameRank;

    #[Embedded(class: UsernameValue::class, columnPrefix: false)]
    private UsernameValue $username;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $verified;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeImmutable $createdAt;

    public function __construct(
        Uuid $id,
        User $user,
        UsernameValue $username,
        ?GameRank $gameRank = null,
        bool $verified = false
    ) {
        $this->id = $id;
        $this->user = $user;
        $this->username = $username;
        $this->gameRank = $gameRank;
        $this->verified = $verified;
        $this->createdAt = new \DateTimeImmutable();
        $this->playerRoles = new ArrayCollection();
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
     * @return Collection<int, PlayerRole>
     */
    public function playerRoles(): Collection
    {
        return $this->playerRoles;
    }

    /**
     * @return array<GameRole>
     */
    public function gameRoles(): array
    {
        return $this->playerRoles->map(fn(PlayerRole $pr) => $pr->gameRole())->toArray();
    }

    public function addRole(GameRole $gameRole): void
    {
        foreach ($this->playerRoles as $playerRole) {
            if ($playerRole->gameRole()->id()->equals($gameRole->id())) {
                return; // Role already exists
            }
        }

        $playerRole = new PlayerRole(
            Uuid::random(),
            $this,
            $gameRole
        );
        $this->playerRoles->add($playerRole);
    }

    public function removeRole(GameRole $gameRole): void
    {
        foreach ($this->playerRoles as $key => $playerRole) {
            if ($playerRole->gameRole()->id()->equals($gameRole->id())) {
                $this->playerRoles->remove($key);
                return;
            }
        }
    }

    public function clearRoles(): void
    {
        $this->playerRoles->clear();
    }

    public function gameRank(): ?GameRank
    {
        return $this->gameRank;
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

    public function update(
        UsernameValue $username,
        ?GameRank $gameRank = null
    ): void {
        $this->username = $username;
        $this->gameRank = $gameRank;
    }

    public function updateRank(?GameRank $gameRank): void
    {
        $this->gameRank = $gameRank;
    }

    public function verify(): void
    {
        $this->verified = true;
    }
}

