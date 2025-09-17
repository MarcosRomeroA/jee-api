<?php
declare(strict_types=1);

namespace App\Contexts\Web\User\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Web\User\Domain\Events\FollowerCreatedDomainEvent;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FollowRepository::class)]
#[ORM\Table(name: "user_follow")]
class Follow extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "following")]
    private User $follower;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "followers")]
    private User $followed;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeInterface $followDate;

    public function __construct(User $follower, User $followed)
    {
        $this->followDate = new \DateTimeImmutable();
        $this->follower = $follower;
        $this->followed = $followed;
    }

    public static function create(User $follower, User $followed): self
    {
        $follow = new self($follower, $followed);
        
        $follow->record(
            new FollowerCreatedDomainEvent(
                $follower->getId(),
                $followed->getId()
            )
        );

        return $follow;
    }

    public function getFollower(): ?User
    {
        return $this->follower;
    }

    public function getFollowed(): ?User
    {
        return $this->followed;
    }

    public function getFollowDate(): \DateTimeInterface
    {
        return $this->followDate;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
