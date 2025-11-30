<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Team\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\Team\Domain\Team;

final class TeamResponse extends Response
{
    public function __construct(
        private readonly string $id,
        private readonly string $name,
        private readonly ?string $description,
        private readonly ?string $image,
        private readonly ?string $creatorId,
        private readonly ?string $creatorUsername,
        private readonly ?string $creatorEmail,
        private readonly int $membersCount,
        private readonly int $gamesCount,
        private readonly bool $disabled,
        private readonly ?string $moderationReason,
        private readonly ?string $disabledAt,
        private readonly string $createdAt,
    ) {
    }

    public static function fromEntity(Team $team): self
    {
        $creator = $team->getCreator();

        return new self(
            id: $team->getId()->value(),
            name: $team->getName(),
            description: $team->getDescription(),
            image: $team->getImage(),
            creatorId: $creator?->getId()->value(),
            creatorUsername: $creator?->getUsername()->value(),
            creatorEmail: $creator?->getEmail()->value(),
            membersCount: $team->getUsersQuantity(),
            gamesCount: $team->getGamesQuantity(),
            disabled: $team->isDisabled(),
            moderationReason: $team->getModerationReason()?->value,
            disabledAt: $team->getDisabledAt()?->format('Y-m-d\TH:i:sP'),
            createdAt: $team->getCreatedAt()->value()->format('Y-m-d\TH:i:sP'),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'image' => $this->image,
            'creatorId' => $this->creatorId,
            'creatorUsername' => $this->creatorUsername,
            'creatorEmail' => $this->creatorEmail,
            'membersCount' => $this->membersCount,
            'gamesCount' => $this->gamesCount,
            'disabled' => $this->disabled,
            'moderationReason' => $this->moderationReason,
            'disabledAt' => $this->disabledAt,
            'createdAt' => $this->createdAt,
        ];
    }
}
