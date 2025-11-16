<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain\ValueObject;

use App\Contexts\Shared\Domain\ValueObject\StringValueObject;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class TeamNameValue extends StringValueObject
{
    #[ORM\Column(name: "name", type: "string", length: 100)]
    protected string $value;

    public function __construct(string $value)
    {
        parent::__construct($value);
        $this->ensureIsNotEmpty();
        $this->limitedToLength(100);
    }

    private function ensureIsNotEmpty(): void
    {
        if (empty(trim($this->value))) {
            throw new \InvalidArgumentException("Team name cannot be empty");
        }
    }
}
