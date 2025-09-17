<?php declare(strict_types=1);

namespace App\Contexts\Web\Notification\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class NotificationType
{
    const NEW_MESSAGE = 'NEW_MESSAGE';
    const NEW_FOLLOWER = 'NEW_FOLLOWER';
    const POST_COMMENTED = 'POST_COMMENTED';
    const POST_LIKED = 'POST_LIKED';
    const POST_SHARED = 'POST_SHARED';
    
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', length: 36)]
    private Uuid $id;

    #[ORM\Column(type: 'string', length: 100, unique: true)]
    private string $name;

    public function __construct(
        Uuid $id,
        string $name,
    )
    {
        $this->id = $id;
        $this->name = $name;
    }

    public static function create(
        Uuid $id,
        string $name,
    ): self
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
