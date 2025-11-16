<?php

declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Infrastructure;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Conversation\Domain\Conversation;
use App\Contexts\Web\Conversation\Domain\ConversationRepository;
use App\Contexts\Web\Conversation\Domain\Exception\ConversationNotFoundException;
use App\Contexts\Web\User\Domain\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Conversation>
 *
 * @method Conversation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conversation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conversation[]    findAll()
 * @method Conversation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class MysqlConversationRepository extends ServiceEntityRepository implements ConversationRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }

    public function save(Conversation $conversation): void
    {
        $this->getEntityManager()->persist($conversation);
        $this->getEntityManager()->flush();
    }

    public function searchConversationByParticipantUsers(
        User $user1,
        User $user2,
    ): ?Conversation {
        return $this->createQueryBuilder("c")
            ->join("c.participants", "p1")
            ->join("c.participants", "p2")
            ->where("p1.user = :user1")
            ->andWhere("p2.user = :user2")
            ->andWhere("p1.id != p2.id")
            ->setParameter("user1", $user1)
            ->setParameter("user2", $user2)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function searchConversations(User $user): array
    {
        return $this->createQueryBuilder("c")
            ->join("c.participants", "p")
            ->where("p.user = :user")
            ->setParameter("user", $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Uuid $id
     * @return Conversation
     */
    public function findByIdOrFail(Uuid $id): Conversation
    {
        $conversation = $this->find($id);

        if (!$conversation) {
            throw new ConversationNotFoundException();
        }

        return $conversation;
    }
}
