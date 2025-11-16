<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class TeamRequestCollectionResponse extends Response
{
    /**
     * @param TeamRequestResponse[] $requests
     */
    public function __construct(public readonly array $requests) {}

    public function toArray(): array
    {
        return [
            "requests" => array_map(
                fn(TeamRequestResponse $request) => $request->toArray(),
                $this->requests,
            ),
        ];
    }
}
