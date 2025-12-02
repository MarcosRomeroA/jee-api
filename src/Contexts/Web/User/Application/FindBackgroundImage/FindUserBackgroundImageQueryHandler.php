<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\FindBackgroundImage;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class FindUserBackgroundImageQueryHandler implements QueryHandler
{
    public function __construct(
        private UserBackgroundImageFinder $finder,
    ) {
    }

    public function __invoke(FindUserBackgroundImageQuery $query): BackgroundImageResponse
    {
        return $this->finder->__invoke(new Uuid($query->userId));
    }
}
