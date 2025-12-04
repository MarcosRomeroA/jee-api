<?php

declare(strict_types=1);

namespace App\Contexts\Web\Event\Domain\ValueObject;

use App\Contexts\Shared\Domain\ValueObject\StringValueObject;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class EventNameValue extends StringValueObject
{
    #[ORM\Column(name: "name", type: "string", length: 255)]
    protected string $value;

    public function __construct(string $value)
    {
        parent::__construct($value);
        $this->ensureIsNotEmpty();
        $this->limitedToLength(255);
    }

    private function ensureIsNotEmpty(): void
    {
        if (empty(trim($this->value))) {
            throw new \InvalidArgumentException("Event name cannot be empty");
        }
    }
}
