<?php

declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\Traits\Timestamps;
use App\Contexts\Shared\Domain\ValueObject\CreatedAtValue;
use App\Contexts\Shared\Domain\ValueObject\UpdatedAtValue;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Conversation\Domain\Events\ConversationCreatedDomainEvent;
use App\Contexts\Web\Participant\Domain\Participant;
use App\Contexts\Web\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConversationRepository::class)]
class Conversation extends AggregateRoot
{
    use Timestamps;
    #[ORM\Id]
    #[ORM\Column(type: "uuid", length: 36)]
    private Uuid $id;

    #[
        ORM\OneToMany(
            targetEntity: Participant::class,
            mappedBy: "conversation",
            cascade: ["persist", "remove"],
        ),
    ]
    private Collection $participants;

    #[
        ORM\OneToMany(
            targetEntity: Message::class,
            mappedBy: "conversation",
            cascade: ["persist", "remove"],
        ),
    ]
    private Collection $messages;

    #[ORM\ManyToOne(targetEntity: Message::class)]
    #[ORM\JoinColumn(name: "last_message_id", referencedColumnName: "id", nullable: true)]
    private ?Message $lastMessage = null;

    /**
     * @param Uuid $id
     */
    private function __construct(Uuid $id)
    {
        $this->id = $id;
        $this->participants = new ArrayCollection();
        $this->createdAt = new CreatedAtValue();
        $this->updatedAt = UpdatedAtValue::now();
    }

    public static function create(Uuid $id): Conversation
    {
        $conversation = new self($id);

        $conversation->record(new ConversationCreatedDomainEvent($id));

        return $conversation;
    }

    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function addParticipant(Participant $participant): void
    {
        foreach ($this->participants as $p) {
            if ($participant->getId() === $p->getId()) {
                return;
            }
        }

        $this->participants->add($participant);
    }

    public function containsParticipant(User $user): bool
    {
        /**
         * @var Participant $p
         */
        foreach ($this->participants as $p) {
            if ($user->getId() === $p->getUser()->getId()) {
                return true;
            }
        }

        return false;
    }

    public function getOtherParticipant(User $user): ?Participant
    {
        foreach ($this->participants as $p) {
            if ($user->getId() !== $p->getUser()->getId()) {
                return $p;
            }
        }

        return null;
    }

    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function getLastMessage(): ?Message
    {
        return $this->lastMessage;
    }

    public function updateLastMessage(Message $message): void
    {
        $this->lastMessage = $message;
        $this->updatedAt = UpdatedAtValue::now();
    }
}
