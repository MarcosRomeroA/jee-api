<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;

interface HashtagRepository
{
    public function save(Hashtag $hashtag): void;

    public function findByTag(string $tag): ?Hashtag;

    public function findById(Uuid $id): ?Hashtag;
}
