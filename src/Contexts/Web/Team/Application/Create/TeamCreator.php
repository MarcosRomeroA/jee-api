<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\Create;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\Team;
use App\Contexts\Web\Team\Domain\TeamRepository;
use App\Contexts\Web\Team\Domain\ValueObject\TeamNameValue;
use App\Contexts\Web\Team\Domain\ValueObject\TeamDescriptionValue;
use App\Contexts\Web\Team\Domain\ValueObject\TeamImageValue;
use App\Contexts\Web\User\Domain\UserRepository;

final class TeamCreator
{
    public function __construct(
        private readonly TeamRepository $teamRepository,
        private readonly UserRepository $userRepository,
    ) {}

    public function create(
        Uuid $id,
        string $name,
        ?string $description,
        ?string $image,
        Uuid $creatorId,
    ): void {
        $creator = $this->userRepository->findById($creatorId);

        $team = Team::create(
            $id,
            new TeamNameValue($name),
            new TeamDescriptionValue($description),
            new TeamImageValue($image),
            $creator,
        );

        $this->teamRepository->save($team);
    }
}
