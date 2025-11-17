<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\ValueObject\SocialNetworkUsername;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'user_social_network')]
#[ORM\UniqueConstraint(name: 'unique_user_social_network', columns: ['user_id', 'social_network_id'])]
class UserSocialNetwork
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private string $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\ManyToOne(targetEntity: SocialNetwork::class)]
    #[ORM\JoinColumn(name: 'social_network_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private SocialNetwork $socialNetwork;

    #[ORM\Embedded(class: SocialNetworkUsername::class, columnPrefix: false)]
    private SocialNetworkUsername $username;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $deletedAt;

    public function __construct(
        Uuid $id,
        User $user,
        SocialNetwork $socialNetwork,
        SocialNetworkUsername $username
    ) {
        $this->id = $id->value();
        $this->user = $user;
        $this->socialNetwork = $socialNetwork;
        $this->username = $username;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->deletedAt = null;
    }

    public function id(): Uuid
    {
        return new Uuid($this->id);
    }

    public function user(): User
    {
        return $this->user;
    }

    public function socialNetwork(): SocialNetwork
    {
        return $this->socialNetwork;
    }

    public function username(): SocialNetworkUsername
    {
        return $this->username;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function deletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function updateUsername(SocialNetworkUsername $username): void
    {
        $this->username = $username;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function delete(): void
    {
        $this->deletedAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function restore(): void
    {
        $this->deletedAt = null;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }
}
