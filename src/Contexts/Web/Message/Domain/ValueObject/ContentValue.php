<?php declare(strict_types=1);

namespace App\Contexts\Web\Message\Domain\ValueObject;

use App\Contexts\Shared\Domain\ValueObject\StringValueObject;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class ContentValue extends StringValueObject
{
    #[ORM\Column(name:'content', type: 'text', length: 255)]
    protected string $value;

    public function __construct(string $value)
    {
        parent::__construct($value);
        $this->limitedToLength(255);
    }
}