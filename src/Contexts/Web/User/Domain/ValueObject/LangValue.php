<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Domain\ValueObject;

use App\Contexts\Shared\Domain\ValueObject\StringValueObject;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class LangValue extends StringValueObject
{
    public const SPANISH = 'es';
    public const ENGLISH = 'en';
    public const PORTUGUESE = 'pt';

    private const VALID_LANGUAGES = [self::SPANISH, self::ENGLISH, self::PORTUGUESE];

    #[ORM\Column(name: 'lang', type: 'string', length: 5, options: ['default' => self::SPANISH])]
    protected string $value;

    public function __construct(string $value)
    {
        $this->validate($value);
        parent::__construct($value);
    }

    private function validate(string $value): void
    {
        if (!in_array($value, self::VALID_LANGUAGES, true)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid language "%s". Valid languages are: %s', $value, implode(', ', self::VALID_LANGUAGES))
            );
        }
    }

    public static function spanish(): self
    {
        return new self(self::SPANISH);
    }

    public static function english(): self
    {
        return new self(self::ENGLISH);
    }

    public static function portuguese(): self
    {
        return new self(self::PORTUGUESE);
    }

    public static function default(): self
    {
        return self::spanish();
    }
}
