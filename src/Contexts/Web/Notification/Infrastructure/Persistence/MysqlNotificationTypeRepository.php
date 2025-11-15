<?php declare(strict_types=1);

namespace App\Contexts\Web\Notification\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Notification\Domain\NotificationType;
use App\Contexts\Web\Notification\Domain\NotificationTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class MysqlNotificationTypeRepository implements NotificationTypeRepository
{
    private EntityRepository $repository;

    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(
            NotificationType::class,
        );
    }

    public function save(NotificationType $notificationType): void
    {
        $this->entityManager->persist($notificationType);
        $this->entityManager->flush();
    }

    public function findById(Uuid $id): ?NotificationType
    {
        return $this->repository->findOneBy(["id" => $id]);
    }

    public function findByName(string $name): NotificationType
    {
        $notificationType = $this->repository->findOneBy(["name" => $name]);

        if (!$notificationType) {
            throw new \RuntimeException(
                "NotificationType with name '{$name}' not found",
            );
        }

        return $notificationType;
    }

    public function exists(Uuid $id): bool
    {
        return $this->repository->findOneBy(["id" => $id]) !== null;
    }

    public function existsByName(string $name): bool
    {
        return $this->repository->findOneBy(["name" => $name]) !== null;
    }

    public function searchAll(): array
    {
        return $this->repository->findBy([], ["name" => "ASC"]);
    }

    public function delete(Uuid $id): void
    {
        $notificationType = $this->findById($id);
        if ($notificationType) {
            $this->entityManager->remove($notificationType);
            $this->entityManager->flush();
        }
    }
}
