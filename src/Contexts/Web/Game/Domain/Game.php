<?php declare(strict_types=1);

namespace App\Contexts\Web\Game\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\ValueObject\DescriptionValue;
use App\Contexts\Web\Game\Domain\ValueObject\ImageValue;
use App\Contexts\Web\Game\Domain\ValueObject\NameValue;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embedded;

//#[ORM\Entity(repositoryClass: GameRepository::class)]
//#[ORM\Table(name: 'game')]
class Game extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', length: 36)]
    private Uuid $id;

    #[Embedded(class: NameValue::class, columnPrefix: false)]
    private NameValue $name;

    #[Embedded(class: DescriptionValue::class, columnPrefix: false)]
    private DescriptionValue $description;

    /**
     * @var ArrayCollection<GameTag>
     */
    private ArrayCollection $gameTags;

    #[Embedded(class: ImageValue::class, columnPrefix: false)]
    private ImageValue $image;
}