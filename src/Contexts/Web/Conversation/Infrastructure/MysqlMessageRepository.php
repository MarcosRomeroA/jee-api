<?php

declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Infrastructure;

use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Conversation\Domain\Message;
use App\Contexts\Web\Conversation\Domain\Conversation;
use App\Contexts\Web\Conversation\Domain\MessageRepository;
use App\Contexts\Web\User\Domain\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Contexts\Web\Conversation\Domain\Exception\MessageNotFoundException;

/**
 * @extends ServiceEntityRepository<Message>
 *
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class MysqlMessageRepository extends ServiceEntityRepository implements MessageRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function save(Message $message): void
    {
        $this->getEntityManager()->persist($message);
        $this->getEntityManager()->flush();
    }

    public function findByIdOrFail(Uuid $id): Message
    {
        $message = $this->find($id);

        if (!$message) {
            throw new MessageNotFoundException();
        }

        return $message;
    }

    public function searchMessages(Conversation $conversation): array
    {
        return $this->createQueryBuilder("m")
            ->where("m.conversation = :conversation")
            ->setParameter("conversation", $conversation)
            ->orderBy("m.createdAt.value", "ASC")
            ->getQuery()
            ->getResult();
    }

    public function markMessagesAsReadForUser(Conversation $conversation, User $reader): int
    {
        return $this->createQueryBuilder("m")
            ->update()
            ->set("m.readAt.value", ":readAt")
            ->where("m.conversation = :conversation")
            ->andWhere("m.user != :reader")
            ->andWhere("m.readAt.value IS NULL")
            ->setParameter("conversation", $conversation)
            ->setParameter("reader", $reader)
            ->setParameter("readAt", new DateTime())
            ->getQuery()
            ->execute();
    }

    public function countUnreadMessagesForUser(Conversation $conversation, User $user): int
    {
        return (int) $this->createQueryBuilder("m")
            ->select("COUNT(m.id)")
            ->where("m.conversation = :conversation")
            ->andWhere("m.user != :user")
            ->andWhere("m.readAt.value IS NULL")
            ->setParameter("conversation", $conversation)
            ->setParameter("user", $user)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
