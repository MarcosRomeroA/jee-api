<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class TournamentRequestCollectionResponse extends Response
{
    /**
     * @param TournamentRequestResponse[] $requests
     */
    public function __construct(public readonly array $requests)
    {
    }

    public function toArray(): array
    {
        return [
            'requests' => array_map(
                fn (TournamentRequestResponse $request) => $request->toArray(),
                $this->requests,
            ),
        ];
    }
}
