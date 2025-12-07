<?php

declare(strict_types=1);

namespace App\Contexts\Web\Event\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Event\Domain\Exception\InvalidEventDateException;
use App\Contexts\Web\Event\Domain\ValueObject\EventDescriptionValue;
use App\Contexts\Web\Event\Domain\ValueObject\EventImageValue;
use App\Contexts\Web\Event\Domain\ValueObject\EventNameValue;
use App\Contexts\Web\Game\Domain\Game;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embedded;

#[ORM\Entity]
#[ORM\Table(name: "event")]
#[ORM\Index(name: "IDX_EVENT_START_AT", columns: ["start_at"])]
#[ORM\Index(name: "IDX_EVENT_TYPE", columns: ["type"])]
class Event extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", length: 36)]
    private Uuid $id;

    #[Embedded(class: EventNameValue::class, columnPrefix: false)]
    private EventNameValue $name;

    #[Embedded(class: EventDescriptionValue::class, columnPrefix: false)]
    private EventDescriptionValue $description;

    #[ORM\ManyToOne(targetEntity: Game::class)]
    #[ORM\JoinColumn(name: "game_id", referencedColumnName: "id", nullable: true)]
    private ?Game $game;

    #[Embedded(class: EventImageValue::class, columnPrefix: false)]
    private EventImageValue $image;

    #[ORM\Column(type: "string", length: 20, enumType: EventType::class)]
    private EventType $type;

    #[ORM\Column(name: "start_at", type: "datetime_immutable")]
    private \DateTimeImmutable $startAt;

    #[ORM\Column(name: "end_at", type: "datetime_immutable")]
    private \DateTimeImmutable $endAt;

    #[ORM\Column(name: "created_at", type: "datetime_immutable")]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: "updated_at", type: "datetime_immutable", nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    private ?\DateTimeImmutable $imageUpdatedAt = null;

    private function __construct(
        Uuid $id,
        EventNameValue $name,
        EventDescriptionValue $description,
        ?Game $game,
        EventImageValue $image,
        EventType $type,
        \DateTimeImmutable $startAt,
        \DateTimeImmutable $endAt,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->game = $game;
        $this->image = $image;
        $this->type = $type;
        $this->startAt = $startAt;
        $this->endAt = $endAt;
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function create(
        Uuid $id,
        EventNameValue $name,
        EventDescriptionValue $description,
        ?Game $game,
        EventImageValue $image,
        EventType $type,
        \DateTimeImmutable $startAt,
        \DateTimeImmutable $endAt,
    ): self {
        self::validateDates($startAt, $endAt);

        return new self(
            $id,
            $name,
            $description,
            $game,
            $image,
            $type,
            $startAt,
            $endAt,
        );
    }

    public function update(
        EventNameValue $name,
        EventDescriptionValue $description,
        ?Game $game,
        EventImageValue $image,
        EventType $type,
        \DateTimeImmutable $startAt,
        \DateTimeImmutable $endAt,
    ): void {
        self::validateDates($startAt, $endAt);

        $this->name = $name;
        $this->description = $description;
        $this->game = $game;
        $this->image = $image;
        $this->type = $type;
        $this->startAt = $startAt;
        $this->endAt = $endAt;
        $this->updatedAt = new \DateTimeImmutable();
    }

    private static function validateDates(\DateTimeImmutable $startAt, \DateTimeImmutable $endAt): void
    {
        if ($endAt < $startAt) {
            throw new InvalidEventDateException("End date cannot be before start date");
        }
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name->value();
    }

    public function getDescription(): ?string
    {
        return $this->description->value();
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function getImage(): ?string
    {
        return $this->image->value();
    }

    public function getType(): EventType
    {
        return $this->type;
    }

    public function getStartAt(): \DateTimeImmutable
    {
        return $this->startAt;
    }

    public function getEndAt(): \DateTimeImmutable
    {
        return $this->endAt;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getImageUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->imageUpdatedAt;
    }

    public function setImageUpdatedAt(?\DateTimeImmutable $imageUpdatedAt): void
    {
        $this->imageUpdatedAt = $imageUpdatedAt;
    }

    public function getImageUrl(string $cdnBaseUrl): ?string
    {
        $filename = $this->image->value();
        if ($filename === null || $filename === '') {
            return null;
        }
        $path = "jee/event/" . $this->id->value() . "/" . $filename;
        $url = rtrim($cdnBaseUrl, '/') . '/' . $path;
        if ($this->imageUpdatedAt !== null) {
            $url .= '?v=' . $this->imageUpdatedAt->getTimestamp();
        }
        return $url;
    }
}
