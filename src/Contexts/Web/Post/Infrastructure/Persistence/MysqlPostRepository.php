<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\Exception\PostAlreadyExistsException;
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
        $post = $this->findOneBy(['id' => $id]);

        if (!$post) {
            throw new PostNotFoundException();
        }

        return $post;
    }

    public function searchFeed(Uuid $userId): array
    {
        $dql = $this
            ->createQueryBuilder('p')
            ->innerJoin('p.user', 'u')
            ->leftJoin('u.followers', 'f')
            ->where( 'f.follower = :userId')
            ->orWhere('u.id = :userId')
            ->setParameter('userId', $userId)
            ->getQuery();

        return $dql->getResult();
    }

    public function checkIsPostExists(Uuid $id): void
    {
        $post = $this->findOneBy(['id' => $id]);

        if ($post) {
            throw new PostAlreadyExistsException();
        }
    }
}