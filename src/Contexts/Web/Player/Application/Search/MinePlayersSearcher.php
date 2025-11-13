<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Application\Search;

use App\Contexts\Shared\Domain\Response\PaginatedResponse;
use App\Contexts\Shared\Domain\ValueObject\Pagination;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Player\Application\Shared\PlayerResponse;
use App\Contexts\Web\Player\Domain\PlayerRepository;

final class MinePlayersSearcher
{
    public function __construct(private readonly PlayerRepository $repository) {}

    public function search(?string $query, Uuid $userId, Pagination $pagination): PaginatedResponse
    {
        $players = $this->repository->searchMineWithPagination(
            $query,
            $userId,
            $pagination->limit(),
            $pagination->offset()
        );

        $totalItems = $this->repository->countMine($query, $userId);

        $data = array_map(
            fn($player) => PlayerResponse::fromPlayer($player)->toArray(),
            $players
        );

        return PaginatedResponse::create(
            $data,
            $pagination->page(),
            $totalItems,
            $pagination->limit()
        );
    }
}

