<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\Create;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\Exception\TeamNotFoundException;
use App\Contexts\Web\Team\Domain\Team;
use App\Contexts\Web\Team\Domain\TeamRepository;
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

        $team = Team::create($id, $name, $description, $image, $creator);

        $this->teamRepository->save($team);
    }
}
