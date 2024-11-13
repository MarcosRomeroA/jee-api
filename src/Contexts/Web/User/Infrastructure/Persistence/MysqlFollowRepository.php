<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Infrastructure\Persistence;

use App\Contexts\Web\User\Domain\Follow;
use App\Contexts\Web\User\Domain\FollowRepository;
use App\Contexts\Web\User\Domain\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class MysqlFollowRepository extends ServiceEntityRepository implements FollowRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Follow::class);
    }

    public function findByFollowerAndFollowed(User $follower, User $followed): ?Follow{
        return $this->findOneBy(['follower' => $follower, 'followed' => $followed]);
    }
}