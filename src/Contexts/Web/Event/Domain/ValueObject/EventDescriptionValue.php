<?php

declare(strict_types=1);

namespace App\Contexts\Web\Event\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class EventDescriptionValue
{
    #[ORM\Column(name: "description", type: "text", nullable: true)]
    private ?string $value;

    public function __construct(?string $value)
    {
        if ($value !== null) {
            $this->ensureIsNotTooLong($value);
        }
        $this->value = $value;
    }

    public function value(): ?string
    {
        return $this->value;
    }

    private function ensureIsNotTooLong(string $value): void
    {
        if (mb_strlen($value) > 5000) {
            throw new \InvalidArgumentException(
                "Event description cannot exceed 5000 characters",
            );
        }
    }
}
