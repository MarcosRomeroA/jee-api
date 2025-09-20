<?php declare(strict_types=1);

namespace App\Contexts\Web\Notification\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Notification\Domain\Notification;
use App\Contexts\Web\Notification\Domain\NotificationRepository;
use App\Contexts\Web\Notification\Domain\Exception\NotificationNotFoundException;
use App\Contexts\Web\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class MysqlNotificationRepository implements NotificationRepository
{
    private EntityRepository $repository;

    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Notification::class);
    }

    public function save(Notification $notification): void
    {
        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }

    public function findById(Uuid $id): Notification
    {
        $notification = $this->repository->find($id);

        if (!$notification) {
            throw new NotificationNotFoundException();
        }

        return $notification;
    }

    public function findByUser(User $user): array
    {
        return $this->repository->findBy(
            ["user" => $user],
            ["createdAt" => "DESC"]
        );
    }

    public function findUnreadByUser(User $user): array
    {
        return $this->repository->findBy(
            ["user" => $user, "readAt" => null],
            ["createdAt" => "DESC"]
        );
    }

    public function exists(Uuid $id): bool
    {
        return $this->repository->findOneBy(["id" => $id]) !== null;
    }

    public function markAsRead(Uuid $id): void
    {
        $notification = $this->repository->find($id);

        if (!$notification) {
            throw new NotificationNotFoundException();
        }

        $notification->markAsRead();
        
        $this->save($notification);
    }

    public function markAllAsReadForUser(User $user): void
    {
        $unreadNotifications = $this->findUnreadByUser($user);

        foreach ($unreadNotifications as $notification) {
            $notification->markAsRead();
        }

        $this->entityManager->flush();
    }

    public function searchByCriteria(?array $criteria): array
    {
        $queryBuilder = $this->repository->createQueryBuilder("n");

        $queryBuilder->orderBy("n.createdAt", "DESC");

        return $queryBuilder->getQuery()->getResult();
    }
}
