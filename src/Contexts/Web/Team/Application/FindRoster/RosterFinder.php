<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\FindRoster;

use App\Contexts\Shared\Domain\FileManager\FileManager;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Application\Shared\RosterResponse;
use App\Contexts\Web\Team\Domain\RosterRepository;

final readonly class RosterFinder
{
    public function __construct(
        private RosterRepository $rosterRepository,
        private FileManager $fileManager,
    ) {
    }

    public function __invoke(Uuid $rosterId): RosterResponse
    {
        $roster = $this->rosterRepository->findById($rosterId);

        return RosterResponse::fromRoster($roster, $this->fileManager);
    }
}

