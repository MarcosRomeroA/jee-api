<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\AddGame;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Exception\GameNotFoundException;
use App\Contexts\Web\Game\Domain\GameRepository;
use App\Contexts\Web\Team\Domain\Exception\TeamNotFoundException;
use App\Contexts\Web\Team\Domain\TeamGameRepository;
use App\Contexts\Web\Team\Domain\TeamRepository;

final readonly class AddGameToTeamCommandHandler implements CommandHandler
{
    public function __construct(
        private TeamRepository $teamRepository,
        private GameRepository $gameRepository,
        private TeamGameRepository $teamGameRepository
    ) {
    }

    public function __invoke(AddGameToTeamCommand $command): void
    {
        $teamId = new Uuid($command->teamId);
        $gameId = new Uuid($command->gameId);

        // Verify team exists
        $team = $this->teamRepository->findById($teamId);
        
        // Verify game exists
        $game = $this->gameRepository->findById($gameId);
        if ($game === null) {
            throw new GameNotFoundException($gameId->value());
        }

        // Check if game is already added to team
        if ($this->teamGameRepository->existsByTeamAndGame($teamId, $gameId)) {
            return; // Game already exists, no need to add again
        }

        // Add game to team
        $team->addGame($game);
        
        $this->teamRepository->save($team);
    }
}
