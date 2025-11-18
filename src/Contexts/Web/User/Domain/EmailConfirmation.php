<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\ValueObject\EmailConfirmationToken;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embedded;

#[ORM\Entity]
#[ORM\Table(name: 'email_confirmation')]
class EmailConfirmation extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', length: 36)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private User $user;

    #[Embedded(class: EmailConfirmationToken::class, columnPrefix: false)]
    private EmailConfirmationToken $token;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $expiresAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $confirmedAt;

    public function __construct(
        Uuid $id,
        User $user,
        EmailConfirmationToken $token,
        \DateTimeImmutable $expiresAt
    ) {
        $this->id = $id;
        $this->user = $user;
        $this->token = $token;
        $this->createdAt = new \DateTimeImmutable();
        $this->expiresAt = $expiresAt;
        $this->confirmedAt = null;
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function user(): User
    {
        return $this->user;
    }

    public function token(): EmailConfirmationToken
    {
        return $this->token;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function expiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function confirmedAt(): ?\DateTimeImmutable
    {
        return $this->confirmedAt;
    }

    public function isConfirmed(): bool
    {
        return $this->confirmedAt !== null;
    }

    public function isExpired(): bool
    {
        return new \DateTimeImmutable() > $this->expiresAt;
    }

    public function canBeResent(): bool
    {
        $now = new \DateTimeImmutable();
        $hoursSinceCreation = ($now->getTimestamp() - $this->createdAt->getTimestamp()) / 3600;

        return $hoursSinceCreation >= 24;
    }

    public function confirm(): void
    {
        if ($this->isConfirmed()) {
            throw new \DomainException('Email already confirmed');
        }

        if ($this->isExpired()) {
            throw new \DomainException('Confirmation token has expired');
        }

        $this->confirmedAt = new \DateTimeImmutable();
    }
}
