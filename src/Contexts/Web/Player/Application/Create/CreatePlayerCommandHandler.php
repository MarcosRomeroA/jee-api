<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Application\Create;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\GameRepository;
use App\Contexts\Web\Player\Domain\Exception\GameNotFoundException;
use App\Contexts\Web\Player\Domain\Exception\GameRankNotFoundException;
use App\Contexts\Web\Player\Domain\Exception\GameRoleNotFoundException;
use App\Contexts\Web\Player\Domain\Exception\UserNotFoundException;
use App\Contexts\Web\Player\Domain\Player;
use App\Contexts\Web\Player\Domain\PlayerRepository;
use App\Contexts\Web\Player\Domain\ValueObject\UsernameValue;
use App\Contexts\Web\User\Domain\UserRepository;

final class CreatePlayerCommandHandler implements CommandHandler
{
    public function __construct(
        private readonly PlayerRepository $playerRepository,
        private readonly UserRepository $userRepository,
        private readonly GameRepository $gameRepository,
        private readonly PlayerCreator $creator
    ) {
    }

    public function __invoke(CreatePlayerCommand $command): void
    {
        $this->creator->create(
            new Uuid($command->id),
            new Uuid($command->userId),
            new Uuid($command->gameId),
            new Uuid($command->gameRoleId),
            new Uuid($command->gameRankId),
            new UsernameValue($command->username)
        );
    }
}

