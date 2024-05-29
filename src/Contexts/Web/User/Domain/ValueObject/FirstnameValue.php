<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Domain\ValueObject;

use App\Contexts\Shared\Domain\ValueObject\StringValueObject;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class FirstnameValue extends StringValueObject
{
    #[ORM\Column(name:'firstname', type: 'string', length: 255)]
    protected string $value;

    public function __construct(string $value)
    {
        parent::__construct($value);
    }
}