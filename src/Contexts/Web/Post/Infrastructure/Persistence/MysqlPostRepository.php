<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\Exception\PostNotFoundException;
use App\Contexts\Web\Post\Domain\Post;
use App\Contexts\Web\Post\Domain\PostRepository;
use App\Contexts\Web\User\Domain\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class MysqlPostRepository extends ServiceEntityRepository implements PostRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function save(Post $post): void
    {
        $this->getEntityManager()->persist($post);
        $this->getEntityManager()->flush();
    }

    /**
     * @return array<Post>
     */
    public function searchAll(): array
    {
        return $this->findAll();
    }

    public function findByUser(User $user): User
    {
        return $this->findBy(['user' => $user]);
    }

    public function findById(Uuid $id): Post
    {
        $user = $this->findOneBy(['id' => $id]);

        if (!$user) {
            throw new PostNotFoundException();
        }

        return $user;
    }
}