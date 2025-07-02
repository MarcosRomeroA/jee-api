<?php declare(strict_types=1);

namespace App\Contexts\Web\Participant\Domain;

use App\Contexts\Shared\Domain\Traits\Timestamps;
use App\Contexts\Shared\Domain\ValueObject\CreatedAtValue;
use App\Contexts\Shared\Domain\ValueObject\UpdatedAtValue;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Conversation\Domain\Conversation;
use App\Contexts\Web\User\Domain\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
class Participant
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', length: 36)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Conversation::class, inversedBy: 'participants')]
    private Conversation $conversation;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $user;

    #[ORM\Column(type: 'boolean')]
    private bool $creator;

    use Timestamps;

    private function __construct(
        Uuid $id,
        Conversation $conversation,
        User $user,
        bool $creator
    )
    {
        $this->id = $id;
        $this->conversation = $conversation;
        $this->user = $user;
        $this->creator = $creator;
        $this->createdAt = new CreatedAtValue();
        $this->updatedAt = UpdatedAtValue::now();
    }

    public static function create(Uuid $id, Conversation $conversation, User $user, bool $creator): self
    {
        return new self($id, $conversation, $user, $creator);
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

    public function isCreator(): bool
    {
        return $this->creator;
    }
}