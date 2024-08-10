<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\ValueObject;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class CreatedAtValue extends DatetimeValueObject
{
    #[ORM\Column(name:'created_at', type: 'datetime')]
    protected ?DateTime $value;

    public function __construct()
    {
        $value = new DateTime();
        parent::__construct($value);
    }

    public function value(): DateTime
    {
        return $this->value;
    }
}