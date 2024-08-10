<?php declare(strict_types=1);

namespace App\Contexts\Web\Game\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\ValueObject\Rank\CodeValue;
use App\Contexts\Web\Game\Domain\ValueObject\Rank\RankNameValue;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embedded;

//#[ORM\Entity(repositoryClass: RankRepository::class)]
//#[ORM\Table(name: 'rank')]
class Rank
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', length: 36)]
    private Uuid $id;

    #[Embedded(class: RankNameValue::class, columnPrefix: false)]
    private RankNameValue $name;

    #[Embedded(class: CodeValue::class, columnPrefix: false)]
    private CodeValue $code;
}
