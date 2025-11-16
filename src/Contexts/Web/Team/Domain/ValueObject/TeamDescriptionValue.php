<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class TeamDescriptionValue
{
    #[
        ORM\Column(
            name: "description",
            type: "text",
            length: 1000,
            nullable: true,
        ),
    ]
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
        if (mb_strlen($value) > 1000) {
            throw new \InvalidArgumentException(
                "Team description cannot exceed 1000 characters",
            );
        }
    }
}
