<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;

interface CommentRepository
{
    /**
     * @param Uuid $id
     * @return Comment
     */
    public function findById(Uuid $id): Comment;
}
