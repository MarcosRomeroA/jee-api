<?php declare(strict_types=1);

namespace App\Contexts\Web\Game\Domain\ValueObject\Rank;

use App\Contexts\Shared\Domain\ValueObject\StringValueObject;
use Doctrine\ORM\Mapping as ORM;

class CodeValue extends StringValueObject
{
    #[ORM\Column(name:'code', type: 'string', length: 255)]
    protected string $value;

    public function __construct(string $value)
    {
        parent::__construct($value);
    }
}