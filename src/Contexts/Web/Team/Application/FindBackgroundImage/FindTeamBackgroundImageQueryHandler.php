<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\FindBackgroundImage;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class FindTeamBackgroundImageQueryHandler implements QueryHandler
{
    public function __construct(
        private TeamBackgroundImageFinder $finder,
    ) {
    }

    public function __invoke(FindTeamBackgroundImageQuery $query): BackgroundImageResponse
    {
        return $this->finder->__invoke(new Uuid($query->teamId));
    }
}
