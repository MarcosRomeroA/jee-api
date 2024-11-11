<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Infrastructure\Persistence;

use App\Contexts\Web\Post\Domain\PostResource;
use App\Contexts\Web\Post\Domain\PostResourceRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MysqlPostResourceRepository extends ServiceEntityRepository implements PostResourceRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostResource::class);
    }

    public function save(PostResource $postResource): void
    {
        $this->getEntityManager()->persist($postResource);
        $this->getEntityManager()->flush();
    }
}