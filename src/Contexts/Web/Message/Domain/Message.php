<?php declare(strict_types=1);

namespace App\Contexts\Web\Message\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\Traits\Timestamps;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Conversation\Domain\Conversation;
use App\Contexts\Web\Message\Domain\ValueObject\ContentValue;
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
}