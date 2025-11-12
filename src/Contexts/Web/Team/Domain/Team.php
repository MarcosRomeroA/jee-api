<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Game;
use App\Contexts\Web\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
#[ORM\Table(name: 'team')]
class Team extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', length: 36)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Game::class)]
    #[ORM\JoinColumn(name: 'game_id', referencedColumnName: 'id', nullable: false)]
    private Game $game;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'owner_id', referencedColumnName: 'id', nullable: false)]
    private User $owner;

    #[ORM\Column(type: 'string', length: 100)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $image;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeImmutable $createdAt;

    /**
     * @var Collection<int, TeamPlayer>
     */
    #[ORM\OneToMany(targetEntity: TeamPlayer::class, mappedBy: 'team', cascade: ['persist', 'remove'])]
    private Collection $teamPlayers;

    public function __construct(
        Uuid $id,
        Game $game,
        User $owner,
        string $name,
        ?string $image = null
    ) {
        $this->id = $id;
        $this->game = $game;
        $this->owner = $owner;
        $this->name = $name;
        $this->image = $image;
        $this->createdAt = new \DateTimeImmutable();
        $this->teamPlayers = new ArrayCollection();
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function game(): Game
    {
        return $this->game;
    }

    public function owner(): User
    {
        return $this->owner;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function image(): ?string
    {
        return $this->image;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function teamPlayers(): Collection
    {
        return $this->teamPlayers;
    }

    public function playersQuantity(): int
    {
        return $this->teamPlayers->count();
    }

    public function update(string $name, ?string $image): void
    {
        $this->name = $name;
        if ($image !== null) {
            $this->image = $image;
        }
    }

    public function isOwner(Uuid $userId): bool
    {
        return $this->owner->id()->equals($userId);
    }
}

