<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\ValueObject;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class UpdatedAtValue extends DatetimeValueObject
{
    #[ORM\Column(name:'updated_at', type: 'datetime')]
    protected ?DateTime $value;

    public function __construct(DateTime $value)
    {
        parent::__construct($value);
    }

    public function value(): DateTime
    {
        return $this->value;
    }

    public static function now(): self
    {
        return new self(new DateTime());
    }
}