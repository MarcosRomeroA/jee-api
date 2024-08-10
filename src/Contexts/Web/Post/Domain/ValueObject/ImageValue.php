<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\ValueObject;

use App\Contexts\Shared\Domain\ValueObject\StringValueObject;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

class ImageValue extends StringValueObject
{
    #[ORM\Column(name:'image', type: Types::STRING, length: 255)]
    protected string $value;

    public function __construct(string $value)
    {
        $this->limitedToLength(255);
        parent::__construct($value);
    }
}