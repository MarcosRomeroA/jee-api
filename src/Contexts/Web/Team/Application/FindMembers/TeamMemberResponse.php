<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\FindMembers;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\Team\Domain\TeamUser;

final class TeamMemberResponse extends Response
{
    public function __construct(
        public readonly string $id,
        public readonly string $firstname,
        public readonly string $lastname,
        public readonly string $username,
        public readonly string $profileImage,
        public readonly bool $isCreator,
        public readonly bool $isLeader,
        public readonly string $joinedAt,
    ) {
    }

    public static function fromTeamUser(TeamUser $teamUser, string $profileImage): self
    {
        $user = $teamUser->getUser();

        return new self(
            $user->getId()->value(),
            $user->getFirstname()->value(),
            $user->getLastname()->value(),
            $user->getUsername()->value(),
            $profileImage,
            $teamUser->isCreator(),
            $teamUser->isLeader(),
            $teamUser->getJoinedAt()->format('Y-m-d\TH:i:s\Z'),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'username' => $this->username,
            'profileImage' => $this->profileImage,
            'isCreator' => $this->isCreator,
            'isLeader' => $this->isLeader,
            'joinedAt' => $this->joinedAt,
        ];
    }
}
