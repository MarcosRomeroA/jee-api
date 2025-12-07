<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\ValueObject\LangValue;
use App\Contexts\Web\User\Domain\ValueObject\ThemeValue;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embedded;

#[ORM\Entity]
#[ORM\Table(name: 'user_preference')]
class UserPreference extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', length: 36)]
    private Uuid $id;

    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[Embedded(class: ThemeValue::class, columnPrefix: false)]
    private ThemeValue $theme;

    #[Embedded(class: LangValue::class, columnPrefix: false)]
    private LangValue $lang;

    private function __construct(
        Uuid $id,
        User $user,
        ThemeValue $theme,
        LangValue $lang,
    ) {
        $this->id = $id;
        $this->user = $user;
        $this->theme = $theme;
        $this->lang = $lang;
    }

    public static function create(
        Uuid $id,
        User $user,
        ThemeValue $theme,
        LangValue $lang,
    ): self {
        return new self($id, $user, $theme, $lang);
    }

    public static function createDefault(Uuid $id, User $user): self
    {
        return new self(
            $id,
            $user,
            ThemeValue::default(),
            LangValue::default(),
        );
    }

    public function update(ThemeValue $theme, LangValue $lang): self
    {
        $this->theme = $theme;
        $this->lang = $lang;

        return $this;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getTheme(): ThemeValue
    {
        return $this->theme;
    }

    public function getLang(): LangValue
    {
        return $this->lang;
    }
}
