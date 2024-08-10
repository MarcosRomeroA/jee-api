<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\ValueObject;

use App\Contexts\Shared\Domain\ValueObject\StringValueObject;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class BodyValue extends StringValueObject
{
    #[ORM\Column(name:'body', type: 'text', length: 512)]
    protected string $value;

    public function __construct(string $value)
    {
        parent::__construct($value);
        $this->limitedToLength(512);
    }
}