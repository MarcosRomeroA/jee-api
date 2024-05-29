<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Domain\ValueObject;

use App\Contexts\Shared\Domain\ValueObject\StringValueObject;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class LastnameValue extends StringValueObject
{
    #[ORM\Column(name:'lastname', type: 'string', length: 255, nullable: true)]
    protected string $value;

    public function __construct(string $value)
    {
        parent::__construct($value);
    }
}