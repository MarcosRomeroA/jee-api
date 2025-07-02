<?php declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\Traits\Timestamps;
use App\Contexts\Shared\Domain\ValueObject\CreatedAtValue;
use App\Contexts\Shared\Domain\ValueObject\UpdatedAtValue;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Conversation\Domain\ValueObject\ContentValue;
use App\Contexts\Web\User\Domain\User;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embedded;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', length: 36)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Conversation::class)]
    private Conversation $conversation;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $user;

    #[Embedded(class: ContentValue::class, columnPrefix: false)]
    private ContentValue $content;

    use Timestamps;

    public function __construct(
        Uuid $id,
        Conversation $conversation,
        User $user,
        ContentValue $content
    )
    {
        $this->id = $id;
        $this->conversation = $conversation;
        $this->user = $user;
        $this->content = $content;
        $this->createdAt = new CreatedAtValue();
        $this->updatedAt = UpdatedAtValue::now();
    }

    public static function create(Uuid $id, Conversation $conversation, User $user, ContentValue $content): self
    {
        return new self($id, $conversation, $user, $content);
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
}