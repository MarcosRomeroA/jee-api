<?php declare(strict_types=1);

namespace App\Contexts\Web\Game\Domain\ValueObject;

use App\Contexts\Shared\Domain\ValueObject\StringValueObject;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class DescriptionValue extends StringValueObject
{
    #[ORM\Column(name:'description', type: 'text', length: 512, unique: true)]
    protected string $value;

    public function __construct(string $value)
    {
        $this->limitedToLength(512);
        parent::__construct($value);
    }
}