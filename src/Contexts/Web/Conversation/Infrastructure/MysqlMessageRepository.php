<?php declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Infrastructure;

use Doctrine\Persistence\ManagerRegistry;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Conversation\Domain\Message;
use App\Contexts\Web\Conversation\Domain\Conversation;
use App\Contexts\Web\Conversation\Domain\MessageRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Contexts\Web\Conversation\Domain\Exception\MessageNotFoundException;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)       
 */
class MysqlMessageRepository extends ServiceEntityRepository implements MessageRepository
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
        $message = $this->find($id->value());
        
        if (!$message) {
            throw new MessageNotFoundException();
        }
        
        return $message;
    }

    public function searchMessages(Conversation $conversation): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.conversation = :conversation')
            ->setParameter('conversation', $conversation)
            ->getQuery()
            ->getResult();
    }
}
