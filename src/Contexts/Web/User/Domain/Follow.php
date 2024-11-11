<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Domain;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FollowRepository::class)]
#[ORM\Table(name: "user_follow")]
class Follow
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "following")]
    #[ORM\JoinColumn(name: "follower_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private ?User $follower;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "followers")]
    #[ORM\JoinColumn(name: "followed_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private ?User $followed;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeInterface $followDate;

    public function __construct()
    {
        $this->followDate = new \DateTimeImmutable();
    }

    public function setFollower(?User $follower): void
    {
        $this->follower = $follower;
    }

    public function setFollowed(?User $followed): void
    {
        $this->followed = $followed;
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
}