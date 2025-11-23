<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'team_user')]
#[ORM\UniqueConstraint(name: 'UNIQ_TEAM_USER', columns: ['team_id', 'user_id'])]
class TeamUser extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', length: 36)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'teamUsers')]
    #[ORM\JoinColumn(name: 'team_id', referencedColumnName: 'id', nullable: false)]
    private Team $team;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private User $user;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $joinedAt;

    public function __construct(
        Uuid $id,
        Team $team,
        User $user
    ) {
        $this->id = $id;
        $this->team = $team;
        $this->user = $user;
        $this->joinedAt = new \DateTimeImmutable();
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function team(): Team
    {
        return $this->team;
    }

    public function user(): User
    {
        return $this->user;
    }

    public function joinedAt(): \DateTimeImmutable
    {
        return $this->joinedAt;
    }
}
