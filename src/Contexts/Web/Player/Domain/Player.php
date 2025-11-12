<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Player\Domain\ValueObject\UsernameValue;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\Game\Domain\GameRole;
use App\Contexts\Web\Game\Domain\GameRank;
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

    #[ORM\ManyToOne(targetEntity: GameRole::class)]
    #[ORM\JoinColumn(name: 'game_role_id', referencedColumnName: 'id', nullable: false)]
    private GameRole $gameRole;

    #[ORM\ManyToOne(targetEntity: GameRank::class)]
    #[ORM\JoinColumn(name: 'game_rank_id', referencedColumnName: 'id', nullable: false)]
    private GameRank $gameRank;

    #[Embedded(class: UsernameValue::class, columnPrefix: false)]
    private UsernameValue $username;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $verified;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeImmutable $createdAt;

    public function __construct(
        Uuid $id,
        User $user,
        GameRole $gameRole,
        GameRank $gameRank,
        UsernameValue $username,
        bool $verified = false
    ) {
        $this->id = $id;
        $this->user = $user;
        $this->gameRole = $gameRole;
        $this->gameRank = $gameRank;
        $this->username = $username;
        $this->verified = $verified;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function user(): User
    {
        return $this->user;
    }

    public function gameRole(): GameRole
    {
        return $this->gameRole;
    }

    public function gameRank(): GameRank
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
        GameRole $gameRole,
        GameRank $gameRank
    ): void {
        $this->username = $username;
        $this->gameRole = $gameRole;
        $this->gameRank = $gameRank;
    }

    public function verify(): void
    {
        $this->verified = true;
    }
}

