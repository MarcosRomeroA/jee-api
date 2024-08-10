<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\ValueObject;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class DeletedAtValue extends DatetimeValueObject
{
    #[ORM\Column(name:'deleted_at', type: 'datetime', nullable: true)]
    protected ?DateTime $value;

    public function __construct(?DateTime $value)
    {
        parent::__construct($value);
    }

    public function value(): ?DateTime
    {
        return $this->value;
    }
}