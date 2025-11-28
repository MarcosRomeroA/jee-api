<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Hashtag\Application\Disable;

use App\Contexts\Backoffice\Hashtag\Domain\Exception\HashtagNotFoundException;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\HashtagRepository;

final readonly class HashtagDisabler
{
    public function __construct(
        private HashtagRepository $repository,
    ) {
    }

    public function __invoke(Uuid $hashtagId): void
    {
        $hashtag = $this->repository->findById($hashtagId);

        if ($hashtag === null) {
            throw new HashtagNotFoundException($hashtagId->value());
        }

        $hashtag->markAsDeleted();
        $this->repository->save($hashtag);
    }
}
