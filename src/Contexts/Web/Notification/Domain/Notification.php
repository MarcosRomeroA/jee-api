<?php declare(strict_types=1);

namespace App\Contexts\Web\Notification\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Conversation\Domain\Message;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\Post\Domain\Post;
use App\Contexts\Web\Notification\Domain\Event\NotificationCreatedEvent;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Notification extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', length: 36)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: NotificationType::class)]
    private NotificationType $notificationType;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $userToNotify;

    #[ORM\ManyToOne(targetEntity: Post::class)]
    private ?Post $post = null;

    #[ORM\ManyToOne(targetEntity: Message::class)]
    private ?Message $message = null;

    #[ORM\Column(type: 'string', length: 36, nullable: true)]
    private ?string $teamId = null;

    #[ORM\Column(type: 'string', length: 36, nullable: true)]
    private ?string $tournamentId = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $readAt = null;

    public function __construct(
        Uuid $id,
        NotificationType $notificationType,
        User $userToNotify,
        ?User $user,
        ?Post $post,
        ?Message $message,
        ?string $teamId = null,
        ?string $tournamentId = null
    )
    {
        $this->id = $id;
        $this->notificationType = $notificationType;
        $this->userToNotify = $userToNotify;
        $this->user = $user;
        $this->post = $post;
        $this->message = $message;
        $this->teamId = $teamId;
        $this->tournamentId = $tournamentId;
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function create(
        Uuid $id,
        NotificationType $notificationType,
        User $userToNotify,
        ?User $user = null,
        ?Post $post = null,
        ?Message $message = null,
        ?string $teamId = null,
        ?string $tournamentId = null
    ): self
    {
        $notification = new self($id, $notificationType, $userToNotify, $user, $post, $message, $teamId, $tournamentId);

        $notification->record(new NotificationCreatedEvent(
            $notification->getId(),
            $notificationType->getName(),
            $userToNotify->getId()->value(),
            $user?->getId()?->value(),
            $post?->getId()?->value(),
            $message?->getId()?->value(),
            $teamId,
            $tournamentId
        ));

        return $notification;
    }

    public function markAsRead(): void
    {
        if ($this->readAt === null) {
            $this->readAt = new \DateTimeImmutable();
        }
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getNotificationType(): NotificationType
    {
        return $this->notificationType;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getReadAt(): ?\DateTimeImmutable
    {
        return $this->readAt;
    }

    public function getIsRead(): bool
    {
        return $this->readAt !== null;
    }

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function getUserToNotify(): User
    {
        return $this->userToNotify;
    }

    public function getTeamId(): ?string
    {
        return $this->teamId;
    }

    public function getTournamentId(): ?string
    {
        return $this->tournamentId;
    }
}
