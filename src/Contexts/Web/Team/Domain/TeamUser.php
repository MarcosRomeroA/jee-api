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
#[ORM\Index(name: 'IDX_TEAM_USER_CREATOR', columns: ['is_creator'])]
#[ORM\Index(name: 'IDX_TEAM_USER_LEADER', columns: ['is_leader'])]
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

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isCreator = false;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isLeader = false;

    public function __construct(
        Uuid $id,
        Team $team,
        User $user,
        bool $isCreator = false,
        bool $isLeader = false
    ) {
        $this->id = $id;
        $this->team = $team;
        $this->user = $user;
        $this->joinedAt = new \DateTimeImmutable();
        $this->isCreator = $isCreator;
        $this->isLeader = $isLeader;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getTeam(): Team
    {
        return $this->team;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getJoinedAt(): \DateTimeImmutable
    {
        return $this->joinedAt;
    }

    public function isCreator(): bool
    {
        return $this->isCreator;
    }

    public function isLeader(): bool
    {
        return $this->isLeader;
    }

    public function setLeader(bool $isLeader): void
    {
        $this->isLeader = $isLeader;
    }
}
