<?php

declare(strict_types=1);

namespace App\Contexts\Web\Player\Application\Create;

use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\GameAccountRequirementRepository;
use App\Contexts\Web\Game\Domain\GameRepository;
use App\Contexts\Web\Game\Domain\GameRoleRepository;
use App\Contexts\Web\Player\Domain\Exception\GameAccountAlreadyInUseException;
use App\Contexts\Web\Player\Domain\Exception\InvalidGameAccountDataException;
use App\Contexts\Web\Player\Domain\Exception\MaxPlayersPerUserExceededException;
use App\Contexts\Web\Player\Domain\Exception\PlayerAlreadyExistsException;
use App\Contexts\Web\Player\Domain\Player;
use App\Contexts\Web\Player\Domain\PlayerRepository;
use App\Contexts\Web\Player\Domain\ValueObject\GameAccountDataValue;
use App\Contexts\Web\User\Domain\UserRepository;

final readonly class PlayerCreator
{
    public function __construct(
        private PlayerRepository $playerRepository,
        private UserRepository $userRepository,
        private GameRepository $gameRepository,
        private GameRoleRepository $gameRoleRepository,
        private GameAccountRequirementRepository $gameAccountRequirementRepository,
        private EventBus $eventBus,
    ) {
    }

    /**
     * @param array<string> $gameRoleIds - Array of GameRole UUIDs (optional)
     */
    public function create(
        Uuid $id,
        Uuid $userId,
        Uuid $gameId,
        array $gameRoleIds,
        GameAccountDataValue $accountData,
    ): void {
        $user = $this->userRepository->findById($userId);
        $game = $this->gameRepository->findById($gameId);

        // Fetch all game roles (optional)
        $gameRoles = [];
        foreach ($gameRoleIds as $gameRoleId) {
            $gameRoles[] = $this->gameRoleRepository->findById(new Uuid($gameRoleId));
        }

        // Validate account data against game requirements
        $this->validateAccountData($gameId, $accountData);

        // Check if player exists without throwing exception
        try {
            $player = $this->playerRepository->findById($id);

            // Validate game account is not already used by same user for same game (for updates)
            $this->validateGameAccountNotInUseByUser($gameId, $userId, $accountData, $id);

            $player->update($gameRoles, $accountData);
        } catch (\Exception $e) {
            // Player doesn't exist, create new one
            if (
                $this->playerRepository->existsByUserIdAndGameId(
                    $userId,
                    $gameId,
                )
            ) {
                throw new PlayerAlreadyExistsException();
            }

            // Validate game account is not already used by same user for same game (for creates)
            $this->validateGameAccountNotInUseByUser($gameId, $userId, $accountData, null);

            // Check max players per user limit (8)
            $currentPlayerCount = $this->playerRepository->countByUserId($userId);
            if ($currentPlayerCount >= 8) {
                throw new MaxPlayersPerUserExceededException();
            }

            $player = Player::create(
                $id,
                $user,
                $game,
                $gameRoles,
                $accountData,
                false,
            );
        }

        $this->playerRepository->save($player);

        $events = $player->pullDomainEvents();
        if (!empty($events)) {
            $this->eventBus->publish(...$events);
        }
    }

    private function validateAccountData(Uuid $gameId, GameAccountDataValue $accountData): void
    {
        $requirement = $this->gameAccountRequirementRepository->findByGameId($gameId);

        if ($requirement === null) {
            return;
        }

        $requirements = $requirement->getRequirements();
        $data = $accountData->value() ?? [];
        $missingFields = [];

        foreach ($requirements as $field => $required) {
            if ($required === true && (!isset($data[$field]) || $data[$field] === '')) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            throw new InvalidGameAccountDataException($missingFields);
        }
    }

    private function validateGameAccountNotInUseByUser(Uuid $gameId, Uuid $userId, GameAccountDataValue $accountData, ?Uuid $excludePlayerId): void
    {
        // Check for Steam ID duplicates within the same user
        $steamId = $accountData->steamId();
        if ($steamId !== null && $steamId !== '') {
            $existingPlayer = $this->playerRepository->findBySteamIdAndGameForUser($steamId, $gameId, $userId, $excludePlayerId);
            if ($existingPlayer !== null) {
                throw new GameAccountAlreadyInUseException($steamId);
            }
        }

        // Check for Riot account duplicates (username + tag) within the same user
        $username = $accountData->username();
        $tag = $accountData->tag();
        if ($username !== null && $username !== '' && $tag !== null && $tag !== '') {
            $existingPlayer = $this->playerRepository->findByRiotAccountAndGameForUser($username, $tag, $gameId, $userId, $excludePlayerId);
            if ($existingPlayer !== null) {
                throw new GameAccountAlreadyInUseException($username . '#' . $tag);
            }
        }
    }
}
