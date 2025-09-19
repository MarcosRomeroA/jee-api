<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\Comment;
use App\Contexts\Web\Post\Domain\CommentRepository;
use App\Contexts\Web\Post\Domain\Exception\CommentNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class MysqlCommentRepository extends ServiceEntityRepository implements CommentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function findById(Uuid $id): Comment
    {
        $comment = $this->findOneBy(['id' => $id]);
        if (!$comment) {
            throw new CommentNotFoundException('Comment not found');
        }
        return $comment;
    }
}