<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\ValueObject\SocialNetworkCode;
use App\Contexts\Web\User\Domain\ValueObject\SocialNetworkName;
use App\Contexts\Web\User\Domain\ValueObject\SocialNetworkUrl;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'social_network')]
class SocialNetwork
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private string $id;

    #[ORM\Embedded(class: SocialNetworkName::class, columnPrefix: false)]
    private SocialNetworkName $name;

    #[ORM\Embedded(class: SocialNetworkCode::class, columnPrefix: false)]
    private SocialNetworkCode $code;

    #[ORM\Embedded(class: SocialNetworkUrl::class, columnPrefix: false)]
    private SocialNetworkUrl $url;

    public function __construct(
        Uuid $id,
        SocialNetworkName $name,
        SocialNetworkCode $code,
        SocialNetworkUrl $url
    ) {
        $this->id = $id->value();
        $this->name = $name;
        $this->code = $code;
        $this->url = $url;
    }

    public function id(): Uuid
    {
        return new Uuid($this->id);
    }

    public function name(): SocialNetworkName
    {
        return $this->name;
    }

    public function code(): SocialNetworkCode
    {
        return $this->code;
    }

    public function url(): SocialNetworkUrl
    {
        return $this->url;
    }
}
