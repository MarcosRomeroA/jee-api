<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\User;

interface PostRepository
{
    public function save(Post $post): void;

    /**
     * @return array<Post>
     */
    public function searchAll(): array;

    public function findByUser(User $user): User;

    public function findById(Uuid $id): Post;
}