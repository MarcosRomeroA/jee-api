<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\Exception\PostAlreadyExistsException;
use App\Contexts\Web\Post\Domain\Exception\PostNotFoundException;
use App\Contexts\Web\Post\Domain\Post;
use App\Contexts\Web\Post\Domain\PostRepository;
use App\Contexts\Web\User\Domain\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
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

    public function findByUser(User $user): ?array
    {
        return $this->findBy(["user" => $user]);
    }

    public function findById(Uuid $id): Post
    {
        $post = $this->find($id);

        if (!$post) {
            throw new PostNotFoundException();
        }

        return $post;
    }

    /**
     * @param array<Uuid> $ids
     * @return array<Post>
     */
    public function findByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $qb = $this->createQueryBuilder('p')
            ->where('p.id IN (:ids)')
            ->setParameter('ids', $ids);

        return $qb->getQuery()->getResult();
    }

    public function searchFeed(Uuid $userId, ?array $criteria = null): array
    {
        $qb = $this->createQueryBuilder("p")
            ->innerJoin("p.user", "u")
            ->leftJoin("u.followers", "f")
            ->where("(f.follower = :userId OR u.id = :userId)")
            ->andWhere("p.disabled = false")
            ->setParameter("userId", $userId);

        if (!is_null($criteria)) {
            if (isset($criteria["limit"]) && (int) $criteria["limit"] > 0) {
                $qb->setMaxResults((int) $criteria["limit"]);
            }
            if (isset($criteria["offset"]) && (int) $criteria["offset"] >= 0) {
                $qb->setFirstResult((int) $criteria["offset"]);
            }
        }

        return $qb->getQuery()->getResult();
    }
    public function checkIsPostExists(Uuid $id): void
    {
        $post = $this->find($id);

        if ($post) {
            throw new PostAlreadyExistsException();
        }
    }

    public function searchByCriteria(array $criteria): array
    {
        $qb = $this->createQueryBuilder("p")->innerJoin("p.user", "u");

        // Por defecto filtrar posts deshabilitados (para API web)
        $includeDisabled = $criteria["includeDisabled"] ?? false;
        if (!$includeDisabled) {
            $qb->andWhere("p.disabled = false");
        }

        if (isset($criteria["userId"])) {
            $qb->andWhere("u.id = :userId")
                ->setParameter("userId", $criteria["userId"]);
        } elseif (isset($criteria["username"])) {
            $qb->andWhere("u.username.value LIKE :username")
                ->setParameter("username", "%" . $criteria["username"] . "%");
        } elseif (isset($criteria["q"])) {
            $qb->andWhere(
                "p.body.value LIKE :query OR u.username.value LIKE :query",
            )->setParameter("query", "%" . $criteria["q"] . "%");
        }

        if (isset($criteria["email"])) {
            $qb->andWhere("u.email.value LIKE :email")
                ->setParameter("email", "%" . $criteria["email"] . "%");
        }

        if (isset($criteria["postId"])) {
            $qb->andWhere("p.id = :postId")
                ->setParameter("postId", $criteria["postId"]);
        }

        if (isset($criteria["disabled"])) {
            $qb->andWhere("p.disabled = :disabled")
                ->setParameter("disabled", $criteria["disabled"]);
        }

        if (isset($criteria["limit"]) && (int) $criteria["limit"] > 0) {
            $qb->setMaxResults((int) $criteria["limit"]);
        }
        if (isset($criteria["offset"]) && (int) $criteria["offset"] >= 0) {
            $qb->setFirstResult((int) $criteria["offset"]);
        }

        $qb->orderBy('p.createdAt.value', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function findSharesQuantity(Uuid $id): int
    {
        $dql = $this->createQueryBuilder("p")
            ->select("COUNT(p.id)")
            ->where("p.sharedPostId = :id")
            ->setParameter("id", $id)
            ->getQuery();

        return (int) $dql->getSingleScalarResult();
    }

    /**
     * @return array<Post>
     */
    public function findSharesByPostId(Uuid $postId, int $limit, int $offset): array
    {
        return $this->createQueryBuilder("p")
            ->where("p.sharedPostId = :postId")
            ->setParameter("postId", $postId)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('p.createdAt.value', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countSharesByPostId(Uuid $postId): int
    {
        $dql = $this->createQueryBuilder("p")
            ->select("COUNT(p.id)")
            ->where("p.sharedPostId = :postId")
            ->setParameter("postId", $postId)
            ->getQuery();

        return (int) $dql->getSingleScalarResult();
    }

    public function countByCriteria(array $criteria): int
    {
        $qb = $this->createQueryBuilder("p")
            ->select("COUNT(p.id)")
            ->innerJoin("p.user", "u");

        // Por defecto filtrar posts deshabilitados (para API web)
        $includeDisabled = $criteria["includeDisabled"] ?? false;
        if (!$includeDisabled) {
            $qb->andWhere("p.disabled = false");
        }

        if (isset($criteria["userId"])) {
            $qb->andWhere("u.id = :userId")
                ->setParameter("userId", $criteria["userId"]);
        } elseif (isset($criteria["username"])) {
            $qb->andWhere("u.username.value LIKE :username")
                ->setParameter("username", "%" . $criteria["username"] . "%");
        } elseif (isset($criteria["q"])) {
            $qb->andWhere(
                "p.body.value LIKE :query OR u.username.value LIKE :query",
            )->setParameter("query", "%" . $criteria["q"] . "%");
        }

        if (isset($criteria["email"])) {
            $qb->andWhere("u.email.value LIKE :email")
                ->setParameter("email", "%" . $criteria["email"] . "%");
        }

        if (isset($criteria["postId"])) {
            $qb->andWhere("p.id = :postId")
                ->setParameter("postId", $criteria["postId"]);
        }

        if (isset($criteria["disabled"])) {
            $qb->andWhere("p.disabled = :disabled")
                ->setParameter("disabled", $criteria["disabled"]);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function countFeed(Uuid $userId): int
    {
        $qb = $this->createQueryBuilder("p")
            ->select("COUNT(p.id)")
            ->innerJoin("p.user", "u")
            ->leftJoin("u.followers", "f")
            ->where("(f.follower = :userId OR u.id = :userId)")
            ->andWhere("p.disabled = false")
            ->setParameter("userId", $userId);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @return array<Post>
     */
    public function findByHashtag(string $hashtag, int $limit, int $offset): array
    {
        $normalizedTag = \App\Contexts\Web\Post\Domain\Hashtag::normalize($hashtag);

        return $this->createQueryBuilder("p")
            ->innerJoin("p.hashtags", "h")
            ->where("h.tag = :tag")
            ->andWhere("p.disabled = false")
            ->setParameter("tag", $normalizedTag)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countByHashtag(string $hashtag): int
    {
        $normalizedTag = \App\Contexts\Web\Post\Domain\Hashtag::normalize($hashtag);

        $qb = $this->createQueryBuilder("p")
            ->select("COUNT(p.id)")
            ->innerJoin("p.hashtags", "h")
            ->where("h.tag = :tag")
            ->andWhere("p.disabled = false")
            ->setParameter("tag", $normalizedTag)
            ->getQuery();

        return (int) $qb->getSingleScalarResult();
    }

    /**
     * @return array<Post>
     */
    public function findByPopularHashtag(string $hashtag, int $days, int $limit, int $offset): array
    {
        $normalizedTag = \App\Contexts\Web\Post\Domain\Hashtag::normalize($hashtag);
        $date = new \DateTimeImmutable();
        $date = $date->modify("-{$days} days");

        return $this->createQueryBuilder("p")
            ->innerJoin("p.hashtags", "h")
            ->where("h.tag = :tag")
            ->andWhere("h.updatedAt.value >= :date")
            ->andWhere("p.disabled = false")
            ->setParameter("tag", $normalizedTag)
            ->setParameter("date", $date)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('p.createdAt.value', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countByPopularHashtag(string $hashtag, int $days): int
    {
        $normalizedTag = \App\Contexts\Web\Post\Domain\Hashtag::normalize($hashtag);
        $date = new \DateTimeImmutable();
        $date = $date->modify("-{$days} days");

        $qb = $this->createQueryBuilder("p")
            ->select("COUNT(p.id)")
            ->innerJoin("p.hashtags", "h")
            ->where("h.tag = :tag")
            ->andWhere("h.updatedAt.value >= :date")
            ->andWhere("p.disabled = false")
            ->setParameter("tag", $normalizedTag)
            ->setParameter("date", $date)
            ->getQuery();

        return (int) $qb->getSingleScalarResult();
    }

    public function hasUserSharedPost(Uuid $postId, Uuid $userId): bool
    {
        $count = $this->createQueryBuilder("p")
            ->select("COUNT(p.id)")
            ->innerJoin("p.user", "u")
            ->where("p.sharedPostId = :postId")
            ->andWhere("u.id = :userId")
            ->setParameter("postId", $postId)
            ->setParameter("userId", $userId)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $count > 0;
    }

    public function deleteByUserId(Uuid $userId): void
    {
        $posts = $this->createQueryBuilder("p")
            ->innerJoin("p.user", "u")
            ->where("u.id = :userId")
            ->setParameter("userId", $userId)
            ->getQuery()
            ->getResult();

        $em = $this->getEntityManager();
        foreach ($posts as $post) {
            $em->remove($post);
        }
        $em->flush();
    }

    public function nullifySharedPostIdByUserId(Uuid $userId): void
    {
        // Get all post IDs from this user
        $postIds = $this->createQueryBuilder("p")
            ->select("p.id")
            ->innerJoin("p.user", "u")
            ->where("u.id = :userId")
            ->setParameter("userId", $userId)
            ->getQuery()
            ->getResult();

        if (empty($postIds)) {
            return;
        }

        $ids = array_map(fn ($row) => $row['id'], $postIds);

        // Nullify sharedPostId for posts that shared any of these posts
        $this->createQueryBuilder("p")
            ->update()
            ->set("p.sharedPostId", ":null")
            ->where("p.sharedPostId IN (:postIds)")
            ->setParameter("null", null)
            ->setParameter("postIds", $ids)
            ->getQuery()
            ->execute();
    }
}
