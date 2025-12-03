<?php declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Domain\ValueObject;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Contexts\Shared\Domain\ValueObject\DatetimeValueObject;

#[ORM\Embeddable]
class ReadAtValue extends DatetimeValueObject
{
    #[ORM\Column(name: 'read_at', type: 'datetime', nullable: true)]
    protected ?DateTime $value;

    public function __construct(?DateTime $value = null)
    {
        parent::__construct($value);
    }

    public static function now(): self
    {
        return new self(new DateTime());
    }

    public function value(): ?DateTime
    {
        return $this->value;
    }

    public function isRead(): bool
    {
        return $this->value !== null;
    }
}
