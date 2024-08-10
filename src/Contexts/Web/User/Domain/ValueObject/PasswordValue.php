<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Domain\ValueObject;

use App\Contexts\Shared\Domain\ValueObject\PasswordValueObject;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class PasswordValue extends PasswordValueObject
{
    #[ORM\Column(name:'password', type: 'string', length: 512)]
    protected string $value;

    public function __construct(string $value)
    {
        parent::__construct($value);
    }
}