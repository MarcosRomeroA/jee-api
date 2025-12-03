<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class RosterLogoValue
{
    #[ORM\Column(name: "logo", type: "string", length: 255, nullable: true)]
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
        if (mb_strlen($value) > 255) {
            throw new \InvalidArgumentException(
                "Roster logo filename cannot exceed 255 characters"
            );
        }
    }
}
