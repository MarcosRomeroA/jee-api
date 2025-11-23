<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\Team\Domain\TeamRequest;

final class TeamRequestResponse extends Response
{
    public function __construct(
        public readonly string $id,
        public readonly string $teamId,
        public readonly string $teamName,
        public readonly string $userId,
        public readonly string $userNickname,
        public readonly string $status,
        public readonly string $createdAt,
        public readonly ?string $acceptedAt,
    ) {
    }

    public static function fromTeamRequest(TeamRequest $teamRequest): self
    {
        return new self(
            $teamRequest->id()->value(),
            $teamRequest->team()->id()->value(),
            $teamRequest->team()->name(),
            $teamRequest->user()->getId()->value(),
            $teamRequest->user()->getUsername()->value(),
            $teamRequest->status(),
            $teamRequest->createdAt()->format(\DateTimeInterface::ATOM),
            $teamRequest->acceptedAt()?->format(\DateTimeInterface::ATOM),
        );
    }

    public function toArray(): array
    {
        return [
            "id" => $this->id,
            "teamId" => $this->teamId,
            "teamName" => $this->teamName,
            "userId" => $this->userId,
            "userNickname" => $this->userNickname,
            "status" => $this->status,
            "createdAt" => $this->createdAt,
            "acceptedAt" => $this->acceptedAt,
        ];
    }
}
