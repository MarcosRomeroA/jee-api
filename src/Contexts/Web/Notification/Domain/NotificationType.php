<?php

declare(strict_types=1);

namespace App\Contexts\Web\Notification\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class NotificationType
{
    public const NEW_MESSAGE = "new_message";
    public const NEW_FOLLOWER = "new_follower";
    public const POST_COMMENTED = "post_commented";
    public const POST_LIKED = "post_liked";
    public const POST_SHARED = "post_shared";
    public const POST_MODERATED = "post_moderated";
    public const COMMENT_MODERATED = "comment_moderated";
    public const USER_MENTIONED = "user_mentioned";
    public const TEAM_REQUEST_RECEIVED = "team_request_received";
    public const TOURNAMENT_REQUEST_RECEIVED = "tournament_request_received";

    #[ORM\Id]
    #[ORM\Column(type: "uuid", length: 36)]
    private Uuid $id;

    #[ORM\Column(type: "string", length: 100, unique: true)]
    private string $name;

    public function __construct(Uuid $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public static function create(Uuid $id, string $name): self
    {
        return new self($id, $name);
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
