<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Infrastructure\Persistence\Doctrine\DoctrineRepository;
use App\Contexts\Web\Post\Domain\Exception\PostNotFoundException;
use App\Contexts\Web\Post\Domain\Post;
use App\Contexts\Web\Post\Domain\PostRepository;
use App\Contexts\Web\User\Domain\User;

class MysqlPostRepository extends DoctrineRepository implements PostRepository
{
    public function save(Post $post): void
    {
        $this->persistAndFlush($post);
    }

    /**
     * @return array<Post>
     */
    public function searchAll(): array
    {
        return $this->repository(Post::class)->findAll();
    }

    public function findByUser(User $user): User
    {
        return $this->repository(Post::class)->findBy(['user' => $user]);
    }

    public function findById(Uuid $id): Post
    {
        $user = $this->repository(Post::class)->findOneBy(['id' => $id]);

        if (!$user) {
            throw new PostNotFoundException();
        }

        return $user;
    }
}