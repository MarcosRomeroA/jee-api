<?php

declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Domain;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embedded;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Domain\Traits\Timestamps;
use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\CreatedAtValue;
use App\Contexts\Shared\Domain\ValueObject\UpdatedAtValue;
use App\Contexts\Web\Conversation\Domain\ValueObject\ContentValue;
use App\Contexts\Web\Conversation\Domain\ValueObject\ReadAtValue;
use App\Contexts\Web\Conversation\Domain\Events\MessageCreatedEvent;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message extends AggregateRoot
{
    use Timestamps;
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', length: 36)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Conversation::class, inversedBy: "messages")]
    private Conversation $conversation;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $user;

    #[Embedded(class: ContentValue::class, columnPrefix: false)]
    private ContentValue $content;

    #[Embedded(class: ReadAtValue::class, columnPrefix: false)]
    private ReadAtValue $readAt;

    public function __construct(
        Uuid $id,
        Conversation $conversation,
        User $user,
        ContentValue $content
    ) {
        $this->id = $id;
        $this->conversation = $conversation;
        $this->user = $user;
        $this->content = $content;
        $this->readAt = new ReadAtValue(null);
        $this->createdAt = new CreatedAtValue();
        $this->updatedAt = UpdatedAtValue::now();
    }

    public static function create(Uuid $id, Conversation $conversation, User $user, ContentValue $content): self
    {
        $message = new self($id, $conversation, $user, $content);

        $message->record(new MessageCreatedEvent(
            $message->getId(),
            $conversation->getId(),
            $user->getId()
        ));

        return $message;
    }

    public function getCreatedAt(): CreatedAtValue
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): UpdatedAtValue
    {
        return $this->updatedAt;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getConversation(): Conversation
    {
        return $this->conversation;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getContent(): ContentValue
    {
        return $this->content;
    }

    public function getReadAt(): ReadAtValue
    {
        return $this->readAt;
    }

    public function isRead(): bool
    {
        return $this->readAt->isRead();
    }

    public function markAsRead(): void
    {
        if (!$this->readAt->isRead()) {
            $this->readAt = ReadAtValue::now();
            $this->updatedAt = UpdatedAtValue::now();
        }
    }
}
