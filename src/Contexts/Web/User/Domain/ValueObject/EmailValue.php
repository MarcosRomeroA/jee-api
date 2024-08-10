<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Domain\ValueObject;

use App\Contexts\Shared\Domain\ValueObject\EmailValueObject;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class EmailValue extends EmailValueObject
{
    #[ORM\Column(name:'email', type: 'string', length: 255, unique: true)]
    protected string $value;

    public function __construct(string $value)
    {
        parent::__construct($value);
    }
}