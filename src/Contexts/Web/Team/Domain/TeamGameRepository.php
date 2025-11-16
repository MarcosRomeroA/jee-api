<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Game;

interface TeamGameRepository
{
    public function save(TeamGame $teamGame): void;

    public function findById(Uuid $id): ?TeamGame;

    public function findByTeamAndGame(Team $team, Game $game): ?TeamGame;

    /**
     * @return TeamGame[]
     */
    public function findByTeam(Uuid $teamId): array;

    /**
     * @return TeamGame[]
     */
    public function findByGame(Uuid $gameId): array;

    public function delete(TeamGame $teamGame): void;

    public function existsByTeamAndGame(Uuid $teamId, Uuid $gameId): bool;

    public function remove(TeamGame $teamGame): void;
}
