<?php declare(strict_types=1);

namespace App\Contexts\Web\Game\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
#[ORM\Table(name: "game")]
class Game extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", length: 36)]
    private Uuid $id;

    #[ORM\Column(type: "string", length: 100)]
    private string $name;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description;

    #[ORM\Column(type: "integer")]
    private int $minPlayersQuantity;

    #[ORM\Column(type: "integer")]
    private int $maxPlayersQuantity;

    #[ORM\Column(type: "datetime_immutable")]
    private \DateTimeImmutable $createdAt;

    public function __construct(
        Uuid $id,
        string $name,
        ?string $description,
        int $minPlayersQuantity,
        int $maxPlayersQuantity,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->minPlayersQuantity = $minPlayersQuantity;
        $this->maxPlayersQuantity = $maxPlayersQuantity;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getMinPlayersQuantity(): int
    {
        return $this->minPlayersQuantity;
    }

    public function getMaxPlayersQuantity(): int
    {
        return $this->maxPlayersQuantity;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
