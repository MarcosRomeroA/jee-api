<?php declare(strict_types=1);

namespace App\Contexts\Web\Game\Application\FindAllByGame;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class FindAllGameRolesByGameQuery implements Query
{
    public function __construct(
        public string $gameId
    ) {
    }
}
