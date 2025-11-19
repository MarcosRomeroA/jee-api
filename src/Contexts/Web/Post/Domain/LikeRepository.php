<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;

interface LikeRepository
{
    /**
     * @param Uuid $id
     * @return Like
     */
    public function findById(Uuid $id): Like;
}
